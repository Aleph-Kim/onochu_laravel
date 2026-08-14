<?php

namespace Tests\Feature;

use App\Enums\MusicApp;
use App\Models\Recommend;
use App\Models\RecommendPlay;
use App\Models\Song;
use App\Models\User;
use App\Services\FloApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Traits\FakesFloApi;
use Tests\TestCase;

class RecommendPlayNotificationTest extends TestCase
{
    use RefreshDatabase, FakesFloApi;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    #[TestDox('미노출 재생 기록이 있는 로그인 사용자는 어느 페이지에서든 평가 모달을 본다')]
    public function test_logged_in_user_with_pending_play_sees_modal_on_any_page(): void
    {
        $listener = User::factory()->create();
        $song = Song::factory()->create(['title' => '테스트곡']);
        $recommend = Recommend::factory()->create(['song_id' => $song->id]);
        RecommendPlay::factory()->create(['user_id' => $listener->id, 'recommend_id' => $recommend->id]);

        $response = $this->withSession($this->loginSession($listener))->get('/');

        $response->assertOk();
        $response->assertSee('song-rate-modal', false);
        $response->assertSee('테스트곡');
    }

    #[TestDox('모달은 한 번 노출되면 다시 뜨지 않는다')]
    public function test_modal_not_shown_again_after_first_view(): void
    {
        $listener = User::factory()->create();
        $recommend = Recommend::factory()->create();
        RecommendPlay::factory()->create(['user_id' => $listener->id, 'recommend_id' => $recommend->id]);
        $session = $this->loginSession($listener);

        $this->withSession($session)->get('/');
        $response = $this->withSession($session)->get('/');

        $response->assertDontSee('song-rate-modal', false);
        $this->assertDatabaseMissing('recommend_plays', ['notified_at' => null]);
    }

    #[TestDox('여러 미노출 재생 기록이 있어도 한 번에 모두 노출 완료로 표시된다')]
    public function test_multiple_pending_plays_are_all_marked_notified_at_once(): void
    {
        $listener = User::factory()->create();
        $recommendA = Recommend::factory()->create();
        $recommendB = Recommend::factory()->create();
        RecommendPlay::factory()->create(['user_id' => $listener->id, 'recommend_id' => $recommendA->id]);
        RecommendPlay::factory()->create(['user_id' => $listener->id, 'recommend_id' => $recommendB->id]);

        $this->withSession($this->loginSession($listener))->get('/');

        $this->assertDatabaseCount('recommend_plays', 2);
        $this->assertDatabaseMissing('recommend_plays', ['notified_at' => null]);
    }

    #[TestDox('게스트는 재생 기록이 있어도 모달을 보지 않는다')]
    public function test_guest_does_not_see_modal(): void
    {
        $listener = User::factory()->create();
        $recommend = Recommend::factory()->create();
        RecommendPlay::factory()->create(['user_id' => $listener->id, 'recommend_id' => $recommend->id]);

        $response = $this->get('/');

        $response->assertDontSee('song-rate-modal', false);
    }

    #[TestDox('재생 직후 이동하는 트랜지션 페이지에서는 모달이 뜨지 않고 기존 기록도 그대로 유지된다')]
    public function test_modal_not_shown_on_music_app_redirect_page(): void
    {
        $listener = User::factory()->create(['preferred_music_app' => MusicApp::AppleMusic]);
        $recommender = User::factory()->create();
        $song = Song::factory()->create(['flo_id' => 123]);
        $recommend = Recommend::factory()->create(['song_id' => $song->id, 'user_id' => $recommender->id]);
        $previousRecommend = Recommend::factory()->create();
        RecommendPlay::factory()->create(['user_id' => $listener->id, 'recommend_id' => $previousRecommend->id]);

        $this->mock(FloApiService::class, function ($mock) {
            $mock->shouldReceive('getSongByFloId')
                ->with(123)
                ->andReturn($this->fakeTrackInfo(['flo_id' => 123]));
        });

        $response = $this->withSession($this->loginSession($listener))
            ->get("/music-app/open?id=123&recommend={$recommend->id}");

        $response->assertOk();
        $response->assertViewIs('music-app.redirect');
        $response->assertDontSee('song-rate-modal', false);
        $this->assertDatabaseHas('recommend_plays', [
            'recommend_id' => $previousRecommend->id,
            'notified_at' => null,
        ]);
    }
}
