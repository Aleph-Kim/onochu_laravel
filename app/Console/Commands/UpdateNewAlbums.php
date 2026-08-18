<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DelaysApiRequests;
use App\Models\Artist;
use App\Models\NewAlbum;
use App\Models\NewAlbumArtist;
use App\Models\Recommend;
use App\Models\User;
use App\Notifications\NewAlbumNotification;
use App\Services\FloApiService;
use App\Services\ImageService;
use App\Services\WebPushService;
use App\WebPush\NewAlbumPayload;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class UpdateNewAlbums extends Command
{
    use DelaysApiRequests;

    protected $signature = 'albums:update-new';
    protected $description = '추천을 1번 이상 받은 아티스트의 새 앨범을 new_albums 테이블에 저장';

    public function __construct(
        private FloApiService  $floApi,
        private ImageService   $imageService,
        private WebPushService $webPush,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('UpdateNewAlbums 시작');

        $artists = Artist::popularByRecommends(null);
        $this->info("추천 아티스트 수: " . count($artists));

        $newAlbums = $this->getRecommendedArtistsNewAlbums($artists);
        $this->info("새 앨범 수: " . count($newAlbums));

        $this->saveNewAlbums($newAlbums);
        $this->info('UpdateNewAlbums 완료');
    }

    private function getNewAlbumData(): array
    {
        $this->delay();
        $kpopData = $this->floApi->getNewKpopAlbum();

        $this->delay();
        $popData = $this->floApi->getNewPopAlbum();

        $this->info("K-POP 새 앨범 수: " . count($kpopData['albums_info']));
        $this->info("POP 새 앨범 수: " . count($popData['albums_info']));

        return [
            'albums_info' => $kpopData['albums_info'] + $popData['albums_info'],
            'artists_flo_id' => $kpopData['artists_flo_id'] + $popData['artists_flo_id'],
        ];
    }

    private function getRecommendedArtistsNewAlbums(Collection $artists): array
    {
        $newAlbums = [];
        $newAlbumFloIds = [];
        $newAlbumData = $this->getNewAlbumData();

        $artistsFloId = $artists->pluck('flo_id')->all();
        $matchingIds = array_intersect(array_keys($newAlbumData['artists_flo_id']), $artistsFloId);
        $this->info("매칭된 아티스트 수: " . count($matchingIds));

        foreach ($matchingIds as $artistId) {
            $newAlbum = $newAlbumData['albums_info'][$newAlbumData['artists_flo_id'][$artistId]];
            if (!in_array($newAlbum['flo_id'], $newAlbumFloIds)) {
                $newAlbums[] = $newAlbum;
                $newAlbumFloIds[] = $newAlbum['flo_id'];
            }
        }

        return $newAlbums;
    }

    private function saveNewAlbums(array $albums): void
    {
        $savedCount = 0;
        $savedAlbums = [];
        foreach ($albums as $album) {
            if (NewAlbum::where('flo_id', $album['flo_id'])->exists()) {
                continue;
            }

            $newAlbum = NewAlbum::create([
                'album_title' => $album['title'],
                'album_img_url' => $album['img_url'],
                'flo_id' => $album['flo_id'],
            ]);

            $savedCount++;
            $album['new_album_id'] = $newAlbum->id;
            $savedAlbums[] = $album;

            foreach ($album['artist'] as $artist) {
                NewAlbumArtist::create([
                    'new_album_id' => $newAlbum->id,
                    'artist_name' => $artist['name'],
                    'flo_id' => $artist['flo_id'],
                ]);

                $this->updateArtistImgUrl($artist['flo_id']);
            }
        }

        $this->info("저장된 새 앨범 수: {$savedCount}");

        $this->notifyRecommenders($savedAlbums);
    }

    // 새 앨범을 추천 아티스트로 둔 유저에게 알림 생성 및 웹 푸시 발송 (유저당 1회로 묶음)
    private function notifyRecommenders(array $savedAlbums): void
    {
        if (empty($savedAlbums)) {
            return;
        }

        // 아티스트 flo_id => 해당 아티스트가 참여한 새 앨범 목록
        $artistToAlbums = [];
        foreach ($savedAlbums as $album) {
            foreach ($album['artist'] as $artist) {
                $artistToAlbums[$artist['flo_id']][] = $album;
            }
        }

        // 새 앨범 아티스트를 추천한 (유저, 아티스트) 쌍 조회, 해당 아티스트 알림을 꺼둔 유저는 제외
        $rows = Recommend::query()
            ->join('songs', 'recommends.song_id', '=', 'songs.id')
            ->join('song_artists', 'songs.id', '=', 'song_artists.song_id')
            ->join('artists', 'song_artists.artist_id', '=', 'artists.id')
            ->leftJoin('artist_notification_mutes', function ($join) {
                $join->on('artist_notification_mutes.artist_id', '=', 'artists.id')
                    ->on('artist_notification_mutes.user_id', '=', 'recommends.user_id');
            })
            ->whereIn('artists.flo_id', array_keys($artistToAlbums))
            ->whereNull('artist_notification_mutes.id')
            ->distinct()
            ->get(['recommends.user_id', 'artists.flo_id']);

        // 유저 => 받을 앨범 목록 (앨범 flo_id로 중복 제거)
        $userAlbums = [];
        foreach ($rows as $row) {
            foreach ($artistToAlbums[$row->flo_id] as $album) {
                $userAlbums[$row->user_id][$album['flo_id']] = $album;
            }
        }

        if (empty($userAlbums)) {
            return;
        }

        // 헤더 알림 탭에 표시할 NewAlbum 모델 (아티스트 포함) 캐시
        $newAlbumIds = array_unique(array_column($savedAlbums, 'new_album_id'));
        $newAlbumModels = NewAlbum::with('artists')->whereIn('id', $newAlbumIds)->get()->keyBy('id');

        $users = User::whereIn('id', array_keys($userAlbums))
            ->with('pushSubscriptions')
            ->get()
            ->keyBy('id');

        $notified = 0;
        $sent = 0;
        foreach ($userAlbums as $userId => $albums) {
            $user = $users->get($userId);
            if (!$user) {
                continue;
            }

            // 헤더 알림 탭에 표시할 인앱 알림 생성 (유저 전원 대상, 푸시 구독 여부 무관)
            foreach ($albums as $album) {
                $newAlbumModel = $newAlbumModels->get($album['new_album_id']);
                if ($newAlbumModel) {
                    $user->notify(new NewAlbumNotification($newAlbumModel));
                    $notified++;
                }
            }

            if ($user->pushSubscriptions->isNotEmpty()) {
                $payload = NewAlbumPayload::build(array_values($albums));
                foreach ($user->pushSubscriptions as $subscription) {
                    $this->webPush->queue($subscription, $payload);
                }
                $sent++;
            }
        }

        $this->webPush->flush();

        $this->info("헤더 알림 생성: {$notified}건, 웹 푸시 발송: {$sent}명");
    }

    private function updateArtistImgUrl(int $floId): void
    {
        $this->delay();
        $floArtist = $this->floApi->getArtistByFloId($floId);
        $artist = Artist::where('flo_id', $floId)->first();

        if ($artist && $artist->flo_img_url !== $floArtist['img_url']) {
            $artist->update([
                'flo_img_url' => $floArtist['img_url'],
                'img_url' => $this->imageService->uploadImage($floArtist['img_url'], 'artist'),
            ]);
        }
    }
}
