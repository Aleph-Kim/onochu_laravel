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
        $response->assertRedirectContains('state=' . session('kakao_oauth_state'));
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

    #[TestDox('state가 세션과 다르면 토큰 발급 없이 400을 반환한다')]
    public function test_callback_with_mismatched_state_returns_400(): void
    {
        Http::fake();

        $response = $this->withSession(['kakao_oauth_state' => 'valid-state'])
            ->get('/auth/callback?code=fake-code&state=attacker-state');

        $response->assertStatus(400);
        Http::assertNothingSent();
        $this->assertNull(session('user'));
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

        $guestSessionId = $this->persistSession(['kakao_oauth_state' => 'valid-state']);

        $response = $this->withCookie(config('session.cookie'), $guestSessionId)
            ->get('/auth/callback?code=fake-code&state=valid-state');

        $response->assertRedirect(route('main'));
        $this->assertNotSame($guestSessionId, $response->getCookie(config('session.cookie'))->getValue());
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

        $response = $this->withSession(['kakao_oauth_state' => 'valid-state'])
            ->withCookie('last_url', '/mypage')
            ->get('/auth/callback?code=fake-code&state=valid-state');

        $response->assertRedirect('/mypage');
    }

    #[TestDox('로그아웃하면 세션을 지우고 메인으로 리다이렉트한다')]
    public function test_logout_clears_session_and_redirects(): void
    {
        $user = User::factory()->create();
        $loggedInSessionId = $this->persistSession(['user' => ['id' => $user->id, 'nickname' => $user->nickname]]);

        $response = $this->withCookie(config('session.cookie'), $loggedInSessionId)
            ->get('/auth/logout');

        $response->assertRedirect(route('main'));
        $this->assertNull(session('user'));
        $this->assertNotSame($loggedInSessionId, $response->getCookie(config('session.cookie'))->getValue());
    }

    /**
     * 세션 쿠키로 같은 세션을 이어받도록 핸들러에 저장 후 세션 ID 반환
     */
    private function persistSession(array $data): string
    {
        $this->withSession($data);
        session()->save();

        return session()->getId();
    }
}
