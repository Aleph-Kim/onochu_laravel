<?php

namespace Tests\Feature;

use App\Enums\MusicApp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class MusicAppPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    #[TestDox('비로그인 상태로 설정 화면에 접근하면 로그인 페이지로 리다이렉트한다')]
    public function test_guest_is_redirected_from_settings_page(): void
    {
        $response = $this->get('/mypage/music-app');

        $response->assertRedirect(route('login'));
    }

    #[TestDox('로그인한 사용자는 설정 화면을 볼 수 있다')]
    public function test_authenticated_user_can_view_settings_page(): void
    {
        $user = User::factory()->create(['preferred_music_app' => 'flo']);

        $response = $this->withSession($this->loginSession($user))->get('/mypage/music-app');

        $response->assertOk();
        $response->assertSee('FLO');
    }

    #[TestDox('비로그인 상태로 저장을 요청하면 401을 반환한다')]
    public function test_guest_cannot_save_preference(): void
    {
        $response = $this->postJson('/api/music-app-preference', ['app' => 'youtube']);

        $response->assertStatus(401);
    }

    #[TestDox('로그인한 사용자는 뮤직앱을 저장할 수 있다')]
    public function test_authenticated_user_can_save_preferred_app(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/music-app-preference', ['app' => 'flo']);

        $response->assertOk();
        $this->assertSame(MusicApp::Flo, $user->fresh()->preferred_music_app);
    }

    #[TestDox('redirect 파라미터가 상대 경로면 화면에 그대로 노출한다')]
    public function test_valid_relative_redirect_is_passed_to_view(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->get('/mypage/music-app?redirect=' . urlencode('/song/detail?id=1'));

        $response->assertOk();
        $response->assertSee('data-redirect-url="/song/detail?id=1"', false);
    }

    #[TestDox('redirect 파라미터가 외부 절대경로면 무시한다')]
    public function test_absolute_redirect_is_ignored(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->get('/mypage/music-app?redirect=' . urlencode('https://evil.com'));

        $response->assertOk();
        $response->assertDontSee('data-redirect-url', false);
    }

    #[TestDox('스포티파이도 저장할 수 있다')]
    public function test_spotify_is_a_valid_app_value(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/music-app-preference', ['app' => 'spotify']);

        $response->assertOk();
        $this->assertSame(MusicApp::Spotify, $user->fresh()->preferred_music_app);
    }

    #[TestDox('지원하지 않는 앱 값이면 검증 오류를 반환한다')]
    public function test_invalid_app_value_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/music-app-preference', ['app' => 'bugs']);

        $response->assertStatus(422);
    }
}
