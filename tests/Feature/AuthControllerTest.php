<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestDox('비로그인 상태면 카카오 로그인으로 리다이렉트한다')]
    public function test_login_redirects_to_kakao_when_guest(): void
    {
        $response = $this->get('/auth/login');

        $response->assertRedirectContains('https://kauth.kakao.com/oauth/authorize');
    }

    #[TestDox('이미 로그인한 상태면 메인으로 리다이렉트한다')]
    public function test_login_redirects_to_main_when_already_logged_in(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession(['user' => ['id' => $user->id, 'nickname' => $user->nickname]])
            ->get('/auth/login');

        $response->assertRedirect(route('main'));
    }

    #[TestDox('인가 코드가 없으면 400을 반환한다')]
    public function test_callback_without_code_returns_400(): void
    {
        $response = $this->get('/auth/callback');

        $response->assertStatus(400);
    }

    #[TestDox('콜백 성공 시 유저를 생성하고 로그인 처리한다')]
    public function test_callback_creates_user_and_logs_in(): void
    {
        Http::fake([
            'kauth.kakao.com/oauth/token' => Http::response(['access_token' => 'fake-token']),
            'kapi.kakao.com/v2/user/me' => Http::response([
                'id' => 999888,
                'properties' => ['nickname' => '테스트유저'],
            ]),
        ]);

        $response = $this->get('/auth/callback?code=fake-code');

        $response->assertRedirect(route('main'));
        $this->assertDatabaseHas('users', ['kakao_id' => 999888, 'nickname' => '테스트유저']);
        $this->assertEquals(User::where('kakao_id', 999888)->first()->id, session('user.id'));
    }

    #[TestDox('last_url 쿠키가 있으면 해당 URL로 리다이렉트한다')]
    public function test_callback_redirects_to_last_url_cookie(): void
    {
        Http::fake([
            'kauth.kakao.com/oauth/token' => Http::response(['access_token' => 'fake-token']),
            'kapi.kakao.com/v2/user/me' => Http::response([
                'id' => 999777,
                'properties' => ['nickname' => '테스트유저2'],
            ]),
        ]);

        $response = $this->withCookie('last_url', '/mypage')
            ->get('/auth/callback?code=fake-code');

        $response->assertRedirect('/mypage');
    }

    #[TestDox('로그아웃하면 세션을 지우고 메인으로 리다이렉트한다')]
    public function test_logout_clears_session_and_redirects(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession(['user' => ['id' => $user->id, 'nickname' => $user->nickname]])
            ->get('/auth/logout');

        $response->assertRedirect(route('main'));
        $this->assertNull(session('user'));
    }
}
