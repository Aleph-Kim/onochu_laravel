<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class ArtistNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    #[TestDox('로그인하지 않으면 알림 토글 시 401을 반환한다')]
    public function test_toggle_requires_login(): void
    {
        $artist = Artist::factory()->create();

        $this->postJson("/api/artists/{$artist->id}/notification-toggle")->assertStatus(401);
    }

    #[TestDox('토글할 때마다 아티스트 알림이 꺼지고 켜진다')]
    public function test_toggle_mutes_and_unmutes_artist(): void
    {
        $user = User::factory()->create();
        $artist = Artist::factory()->create();
        $session = $this->loginSession($user);

        $this->withSession($session)->postJson("/api/artists/{$artist->id}/notification-toggle")
            ->assertOk()
            ->assertJsonPath('data.muted', true);
        $this->assertDatabaseHas('artist_notification_mutes', ['user_id' => $user->id, 'artist_id' => $artist->id]);

        $this->withSession($session)->postJson("/api/artists/{$artist->id}/notification-toggle")
            ->assertOk()
            ->assertJsonPath('data.muted', false);
        $this->assertDatabaseMissing('artist_notification_mutes', ['user_id' => $user->id, 'artist_id' => $artist->id]);
    }
}
