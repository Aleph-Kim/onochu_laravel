<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private string $kakaoAuthUrl = 'https://kauth.kakao.com';
    private string $kakaoApiUrl = 'https://kapi.kakao.com';

    public function login(Request $request)
    {
        if (session('user')) {
            return redirect()->route('main');
        }

        // 타인의 인가 코드로 로그인시키는 Login CSRF 방지용 state
        $state = Str::random(40);
        $request->session()->put('kakao_oauth_state', $state);

        $params = [
            'client_id' => config('services.kakao.client_id'),
            'redirect_uri' => config('services.kakao.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'profile_nickname',
            'state' => $state,
        ];

        return redirect($this->kakaoAuthUrl . '/oauth/authorize?' . http_build_query($params));
    }

    public function callback(Request $request)
    {
        abort_unless($request->has('code'), 400, '인증 코드가 없음');

        $state = $request->session()->pull('kakao_oauth_state');
        abort_unless(is_string($state) && hash_equals($state, (string)$request->input('state')), 400, 'state 불일치');

        $token = $this->getAccessToken($request->input('code'));
        $userInfo = $this->getUserInfo($token);

        $user = User::firstOrCreate(
            ['kakao_id' => $userInfo['id']],
            ['nickname' => $userInfo['properties']['nickname']]
        );

        session(['user' => ['id' => $user->id, 'nickname' => $user->nickname]]);

        if ($request->cookie('last_url')) {
            return redirect($request->cookie('last_url'))->cookie('last_url', '', -1);
        }

        return redirect()->route('main');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('main');
    }

    private function getAccessToken(string $code): string
    {
        $response = Http::asForm()->post($this->kakaoAuthUrl . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.kakao.client_id'),
            'client_secret' => config('services.kakao.client_secret'),
            'redirect_uri' => config('services.kakao.redirect_uri'),
            'code' => $code,
        ]);

        abort_unless($response->successful(), 500, '액세스 토큰 발급 실패');

        return $response->json('access_token');
    }

    private function getUserInfo(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get($this->kakaoApiUrl . '/v2/user/me');

        abort_unless($response->successful(), 500, '사용자 정보 조회 실패');

        return $response->json();
    }
}
