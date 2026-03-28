<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\NewAlbum;
use App\Models\NewAlbumArtist;
use App\Services\FloApiService;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateNewAlbums extends Command
{
    protected $signature   = 'albums:update-new';
    protected $description = '추천을 1번 이상 받은 아티스트의 새 앨범을 new_albums 테이블에 저장';

    public function __construct(
        private FloApiService $floApi,
        private ImageService $imageService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('UpdateNewAlbums 시작');

        $artistRows = DB::select("
            SELECT a.flo_id, a.name, a.img_url, COUNT(*) as recommend_cnt
            FROM recommends r
            JOIN songs s ON r.song_id = s.id
            JOIN song_artists sa ON s.id = sa.song_id
            JOIN artists a ON sa.artist_id = a.id
            GROUP BY a.flo_id, a.name, a.img_url
            ORDER BY recommend_cnt DESC, MAX(r.created_at) DESC
        ");
        $artists = array_map(fn($r) => (array) $r, $artistRows);
        $this->info("추천 아티스트 수: " . count($artists));

        $newAlbums = $this->getRecommendedArtistsNewAlbums($artists);
        $this->info("새 앨범 수: " . count($newAlbums));

        $this->saveNewAlbums($newAlbums);
        $this->info('UpdateNewAlbums 완료');
    }

    private function getNewAlbumData(): array
    {
        $kpopData = $this->floApi->getNewKpopAlbum();
        $popData  = $this->floApi->getNewPopAlbum();

        $this->info("K-POP 새 앨범 수: " . count($kpopData['albums_info']));
        $this->info("POP 새 앨범 수: " . count($popData['albums_info']));

        return [
            'albums_info'   => $kpopData['albums_info'] + $popData['albums_info'],
            'artists_flo_id' => $kpopData['artists_flo_id'] + $popData['artists_flo_id'],
        ];
    }

    private function getRecommendedArtistsNewAlbums(array $artists): array
    {
        $newAlbums       = [];
        $newAlbumFloIds  = [];
        $newAlbumData    = $this->getNewAlbumData();

        $artistsFloId = array_column($artists, 'flo_id');
        $matchingIds  = array_intersect(array_keys($newAlbumData['artists_flo_id']), $artistsFloId);
        $this->info("매칭된 아티스트 수: " . count($matchingIds));

        foreach ($matchingIds as $artistId) {
            $newAlbum = $newAlbumData['albums_info'][$newAlbumData['artists_flo_id'][$artistId]];
            if (!in_array($newAlbum['flo_id'], $newAlbumFloIds)) {
                $newAlbums[]      = $newAlbum;
                $newAlbumFloIds[] = $newAlbum['flo_id'];
            }
        }

        return $newAlbums;
    }

    private function saveNewAlbums(array $albums): void
    {
        $savedCount = 0;
        foreach ($albums as $album) {
            if (NewAlbum::where('flo_id', $album['flo_id'])->exists()) {
                continue;
            }

            $newAlbum = NewAlbum::create([
                'album_title'   => $album['title'],
                'album_img_url' => $album['img_url'],
                'flo_id'        => $album['flo_id'],
            ]);

            $savedCount++;

            foreach ($album['artist'] as $artist) {
                NewAlbumArtist::create([
                    'new_album_id' => $newAlbum->id,
                    'artist_name'  => $artist['name'],
                    'flo_id'       => $artist['flo_id'],
                ]);

                $this->updateArtistImgUrl($artist['flo_id']);
            }
        }

        $this->info("저장된 새 앨범 수: {$savedCount}");
    }

    private function updateArtistImgUrl(int $floId): void
    {
        $floArtist = $this->floApi->getArtistByFloId($floId);
        $artist    = Artist::where('flo_id', $floId)->first();

        if ($artist && $artist->flo_img_url !== $floArtist['img_url']) {
            $artist->update([
                'flo_img_url' => $floArtist['img_url'],
                'img_url'     => $this->imageService->uploadImage($floArtist['img_url'], 'artist'),
            ]);
        }
    }
}
