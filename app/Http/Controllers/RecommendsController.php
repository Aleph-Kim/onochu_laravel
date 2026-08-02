<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecommendCreateRequest;
use App\Http\Requests\RecommendStoreRequest;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Recommend;
use App\Models\Song;
use App\Models\User;
use App\Services\FloApiService;
use App\Services\ImageService;

class RecommendsController extends Controller
{
    public function __construct(
        private FloApiService $floApi,
        private ImageService  $imageService,
    )
    {
    }

    public function create(RecommendCreateRequest $request)
    {
        $songId = $request->validated('id');

        $songInfo = $this->floApi->getSongByFloId($songId);
        session(['song_info' => $songInfo]);

        $song = Song::where('flo_id', $songInfo['song']['flo_id'])->first();
        $previousRecommends = $song
            ? Recommend::where('user_id', session('user.id'))->where('song_id', $song->id)->latest()->get()
            : collect();

        return view('recommends.index', compact('songInfo', 'previousRecommends'));
    }

    public function store(RecommendStoreRequest $request)
    {
        $songInfo = session('song_info');

        // 가수 조회 및 저장 (이미지 업로드는 신규 생성 시에만)
        $artists = collect();
        foreach ($songInfo['artists'] as $artistInfo) {
            $artist = Artist::where('flo_id', $artistInfo['flo_id'])->first();
            if (!$artist) {
                $imgUrl = $this->imageService->uploadImage(
                    $artistInfo['img_url'] . '?/dims/resize/1000x1000/quality/90', 'artist'
                );
                $artist = Artist::create(array_merge($artistInfo, [
                    'img_url' => $imgUrl,
                    'flo_img_url' => $artistInfo['img_url'],
                ]));
            }
            $artists->push($artist);
        }

        // 앨범 조회 및 저장
        $album = Album::where('flo_id', $songInfo['album']['flo_id'])->first();
        if (!$album) {
            $imgUrl = $this->imageService->uploadImage(
                $songInfo['album']['img_url'] . '?/dims/resize/1000x1000/quality/90'
            );
            $album = Album::create(array_merge($songInfo['album'], ['img_url' => $imgUrl]));
        }

        // 노래 조회 및 저장
        $song = Song::firstOrCreate(
            ['flo_id' => $songInfo['song']['flo_id']],
            array_merge($songInfo['song'], ['album_id' => $album->id])
        );

        // 노래-아티스트 관계 저장
        $song->artists()->syncWithoutDetaching($artists->pluck('id'));

        // 추천 저장
        $recommend = Recommend::create([
            'song_id' => $song->id,
            'user_id' => session('user.id'),
            'score' => $request->validated('score') ?? 3,
            'comment' => $request->validated('comment'),
        ]);

        return redirect()->route('recommends.show', $recommend)
            ->with('message', '추천이 저장되었습니다.');
    }

    public function show(Recommend $recommend)
    {
        $recommend->loadMissing(['song.album', 'song.artists', 'user']);

        $preferredMusicApp = session('user.id') ? User::find(session('user.id'))?->preferred_music_app : null;

        return view('recommends.detail', compact('recommend', 'preferredMusicApp'));
    }

    public function destroy(Recommend $recommend)
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        if ($recommend->user_id !== session('user.id')) {
            return $this->errorResponse('잘못된 요청입니다.', 400);
        }

        $albumId = $recommend->song->album_id;
        $userId = $recommend->user_id;
        $recommend->delete();

        // 삭제한 추천의 앨범이 프로필 앨범으로 설정되어 있었다면 함께 초기화
        $profileReset = (bool)User::where('id', $userId)
            ->where('profile_album_id', $albumId)
            ->update(['profile_album_id' => null]);

        return $this->successResponse('추천이 삭제되었습니다.', ['profile_reset' => $profileReset]);
    }
}
