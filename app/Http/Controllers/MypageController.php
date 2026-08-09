<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Recommend;
use App\Models\User;

class MypageController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(session('user.id'));
        $data = $this->getMypageInfo($user);

        return view('mypage.show', $data + ['isOwner' => true]);
    }

    public function user(User $user)
    {
        if ($user->id === session('user.id')) {
            return redirect()->route('mypage.index');
        }

        $data = $this->getMypageInfo($user);

        return view('mypage.show', $data + ['isOwner' => false]);
    }

    public function artistRecommends(Artist $artist)
    {
        $user = User::findOrFail(session('user.id'));
        $songList = Recommend::latestPerSong(1000, $user->id)
            ->filter(fn($recommend) => $recommend->song->artists->contains('id', $artist->id))
            ->values();

        if ($songList->isEmpty()) abort(404);

        return view('mypage.artist-recommends', compact('artist', 'songList'));
    }

    public function setProfileAlbum(Recommend $recommend)
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        if ($recommend->user_id !== session('user.id')) {
            return $this->errorResponse('잘못된 요청입니다.', 400);
        }

        $album = $recommend->song->album;

        User::where('id', $recommend->user_id)->update(['profile_album_id' => $album->id]);

        return $this->successResponse('앨범 설정 완료', [
            'album_img_url' => $album->img_url . '?size=1000x1000',
            'album_flo_id' => $album->flo_id,
        ]);
    }

    private function getMypageInfo(User $user): array
    {
        $user->loadStats();
        $artistList = $user->likeArtists();
        $genreList = $user->likeGenres();
        $songList = Recommend::latestPerSong(1000, $user->id);

        return compact('user', 'artistList', 'genreList', 'songList');
    }
}
