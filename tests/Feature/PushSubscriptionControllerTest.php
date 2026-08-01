<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class PushSubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    #[TestDox('로그인하지 않으면 401을 반환한다')]
    public function test_subscribe_requires_login(): void
    {
        $response = $this->postJson('/api/push/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'keys'     => ['p256dh' => 'key', 'auth' => 'auth'],
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    #[TestDox('필수 값이 없으면 422를 반환한다')]
    public function test_subscribe_validation_failure_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/push/subscribe', []);

        $response->assertStatus(422);
    }

    #[TestDox('푸시 구독 정보를 생성한다')]
    public function test_subscribe_creates_push_subscription(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/push/subscribe', [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
                'keys'     => ['p256dh' => 'p256dh-key', 'auth' => 'auth-secret'],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id'    => $user->id,
            'endpoint'   => 'https://fcm.googleapis.com/fcm/send/abc',
            'public_key' => 'p256dh-key',
            'auth_token' => 'auth-secret',
        ]);
    }

    #[TestDox('같은 endpoint로 재구독하면 기존 구독 정보를 갱신한다')]
    public function test_subscribe_updates_existing_subscription_for_same_endpoint(): void
    {
        $user = User::factory()->create();
        $existing = PushSubscription::factory()->create([
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
        ]);

        $this->withSession($this->loginSession($user))
            ->postJson('/api/push/subscribe', [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
                'keys'     => ['p256dh' => 'new-key', 'auth' => 'new-auth'],
            ]);

        $this->assertDatabaseCount('push_subscriptions', 1);
        $this->assertDatabaseHas('push_subscriptions', [
            'id'        => $existing->id,
            'user_id'   => $user->id,
            'public_key' => 'new-key',
        ]);
    }

    #[TestDox('로그인하지 않으면 401을 반환한다')]
    public function test_unsubscribe_requires_login(): void
    {
        $response = $this->postJson('/api/push/unsubscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    #[TestDox('필수 값이 없으면 422를 반환한다')]
    public function test_unsubscribe_validation_failure_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/push/unsubscribe', []);

        $response->assertStatus(422);
    }

    #[TestDox('구독 정보를 삭제한다')]
    public function test_unsubscribe_deletes_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = PushSubscription::factory()->create(['user_id' => $user->id]);

        $response = $this->withSession($this->loginSession($user))
            ->postJson('/api/push/unsubscribe', ['endpoint' => $subscription->endpoint]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertModelMissing($subscription);
    }
}
