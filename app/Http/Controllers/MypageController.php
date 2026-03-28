<?php

namespace App\Http\Controllers;

use App\Models\Recommend;
use App\Models\User;
use Illuminate\Http\Request;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        if (!session('user')) {
            return redirect('/login')->cookie('last_url', '/mypage', 3600);
        }

        $data = $this->getMypageInfo(session('user.id'));

        return view('mypage.index', $data);
    }

    public function user(Request $request)
    {
        $userId = (int) $request->input('id');

        if (!$userId) {
            return redirect('/')->with('error', '잘못된 요청입니다.');
        }

        if ($userId === session('user.id')) {
            return redirect('/mypage');
        }

        $data = $this->getMypageInfo($userId);

        return view('mypage.user', $data);
    }

    public function setProfileAlbum(Request $request)
    {
        if (!session('user')) {
            return response()->json(['code' => 401, 'message' => '로그인이 필요합니다.']);
        }

        $recommendId = (int) $request->input('recommend_id');
        $userId = session('user.id');

        $recommend = Recommend::with('song.album')
            ->where('user_id', $userId)
            ->where('id', $recommendId)
            ->first();

        if (!$recommend) {
            return response()->json(['code' => 400, 'message' => '잘못된 요청입니다.']);
        }

        $album = $recommend->song->album;

        User::where('id', $userId)->update(['profile_album_id' => $album->id]);

        return response()->json([
            'message'       => '앨범 설정 완료',
            'album_img_url' => $album->img_url . '?size=1000x1000',
            'album_flo_id'  => $album->flo_id,
        ]);
    }

    private function getMypageInfo(int $userId): array
    {
        $user = User::findOrFail($userId);

        $userInfo   = $user->infoWithStats();
        $artistList = $user->likeArtists();
        $genreList  = $user->likeGenres();
        $songList   = Recommend::latestPerSong(1000, $userId);

        return compact('userInfo', 'artistList', 'genreList', 'songList');
    }
}
