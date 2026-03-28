<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Recommend;
use App\Models\Song;
use App\Services\FloApiService;
use App\Services\ImageService;
use Illuminate\Http\Request;

class RecommendsController extends Controller
{
    public function __construct(
        private FloApiService $floApi,
        private ImageService $imageService,
    ) {}

    public function index(Request $request)
    {
        if (!session('user')) {
            return redirect('/login')->cookie('last_url', $request->getRequestUri(), 3600);
        }

        $songId = (int) $request->input('id');

        if (empty($songId)) {
            abort(400);
        }

        $songInfo = $this->floApi->getSongByFloId($songId);
        session(['song_info' => $songInfo]);

        return view('recommends.index', compact('songInfo'));
    }

    public function post(Request $request)
    {
        if (!session('user')) {
            return redirect('/login')->cookie('last_url', '/recommends', 3600);
        }

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
                    'img_url'     => $imgUrl,
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
            'score'   => $request->input('score', 3),
            'comment' => $request->input('comment'),
        ]);

        return redirect('/recommends/detail?id=' . $recommend->id)
            ->with('message', '추천이 저장되었습니다.');
    }

    public function detail(Request $request)
    {
        $recommendId = (int) $request->input('id');

        if (empty($recommendId)) {
            abort(400);
        }

        $recommendInfo = Recommend::findWithDetails($recommendId);

        if (!$recommendInfo) {
            abort(400);
        }

        return view('recommends.detail', compact('recommendInfo'));
    }
}
