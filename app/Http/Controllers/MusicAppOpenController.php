<?php

namespace App\Http\Controllers;

use App\Enums\MusicApp;
use App\Http\Requests\SongDetailRequest;
use App\Models\Recommend;
use App\Models\RecommendPlay;
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

        // 본인이 아닌 다른 사용자의 추천을 재생한 경우에만 재생 기록 저장
        $recommendId = $request->validated('recommend');
        if (
            $recommendId
            && ($recommend = Recommend::find($recommendId))
            && $recommend->user_id !== $user->id
        ) {
            RecommendPlay::create(['user_id' => $user->id, 'recommend_id' => $recommend->id]);
        }

        $songInfo = $this->floApi->getSongByFloId($request->validated('id'));
        $url = $songInfo['song']['url'];

        $platformUrl = match ($user->preferred_music_app) {
            MusicApp::AppleMusic => $this->platform->resolveAppleMusicUrl($url['apple_music_keyword']),
            MusicApp::Melon => $this->platform->resolveMelonUrl($url['melon_keyword']),
            MusicApp::Genie => $this->platform->resolveGenieUrl($url['genie_keyword']),
            default => $url[$user->preferred_music_app->value]
        };

        if (!$platformUrl['app']) {
            return redirect()->away($platformUrl['web']);
        }

        return view('music-app.redirect', ['appUrl' => $platformUrl['app'], 'webUrl' => $platformUrl['web']]);
    }
}
