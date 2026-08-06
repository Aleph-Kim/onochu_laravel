<?php

namespace App\Http\Controllers;

use App\Enums\MusicApp;
use App\Http\Requests\SongDetailRequest;
use App\Models\User;
use App\Services\FloApiService;
use App\Services\PlatformService;

class MusicAppOpenController extends Controller
{
    public function __construct(
        private FloApiService   $floApi,
        private PlatformService $platform,
    )
    {
    }

    public function show(SongDetailRequest $request)
    {
        $user = User::findOrFail(session('user.id'));

        if (!$user->preferred_music_app) {
            return redirect()->route('mypage.music-app', ['redirect' => $request->getRequestUri()]);
        }

        $songInfo = $this->floApi->getSongByFloId($request->validated('id'));
        $url = $songInfo['song']['url'];

        $platformUrl = match ($user->preferred_music_app) {
            MusicApp::AppleMusic => $this->platform->resolveAppleMusicUrl($url['apple_music_keyword']),
            MusicApp::Melon => $this->platform->resolveMelonUrl($url['melon_keyword']),
            default => $url[$user->preferred_music_app->value]
        };

        if (!$platformUrl['app']) {
            return redirect()->away($platformUrl['web']);
        }

        return view('music-app.redirect', ['appUrl' => $platformUrl['app'], 'webUrl' => $platformUrl['web']]);
    }
}
