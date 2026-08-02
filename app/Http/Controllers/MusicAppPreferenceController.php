<?php

namespace App\Http\Controllers;

use App\Http\Requests\MusicAppPreferenceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class MusicAppPreferenceController extends Controller
{
    public function index(Request $request)
    {
        $user = User::findOrFail(session('user.id'));

        $redirect = $request->query('redirect');
        $redirectUrl = (is_string($redirect) && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//'))
            ? $redirect
            : null;

        return view('mypage.music-app', [
            'preferredMusicApp' => $user->preferred_music_app,
            'redirectUrl' => $redirectUrl,
        ]);
    }

    public function store(MusicAppPreferenceRequest $request)
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        User::where('id', session('user.id'))
            ->update(['preferred_music_app' => $request->validated('app')]);

        return $this->successResponse('뮤직앱이 저장되었습니다.');
    }
}
