<?php

namespace Tests\Feature;

use App\Models\NewAlbum;
use App\Models\User;
use App\Notifications\NewAlbumNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function loginSession(User $user): array
    {
        return ['user' => ['id' => $user->id, 'nickname' => $user->nickname]];
    }

    private function notifyNewAlbum(User $user, int $albumFloId = 1001): void
    {
        $newAlbum = NewAlbum::create(['album_title' => '새 앨범', 'album_img_url' => null, 'flo_id' => $albumFloId]);
        $user->notify(new NewAlbumNotification($newAlbum));
    }

    #[TestDox('로그인하지 않으면 알림 목록 조회 시 401을 반환한다')]
    public function test_index_requires_login(): void
    {
        $this->getJson('/api/notifications')->assertStatus(401);
    }

    #[TestDox('본인의 알림 목록과 안 읽은 개수를 반환한다')]
    public function test_index_returns_own_notifications(): void
    {
        $user = User::factory()->create();
        $this->notifyNewAlbum($user);
        $this->notifyNewAlbum(User::factory()->create(), 1002);

        $response = $this->withSession($this->loginSession($user))->getJson('/api/notifications');

        $response->assertOk();
        $response->assertJsonCount(1, 'data.notifications');
        $response->assertJsonPath('data.unread_count', 1);
        $response->assertJsonPath('data.notifications.0.unread', true);
    }

    #[TestDox('알림 목록은 20개씩 나뉘고 커서로 다음 목록을 이어서 조회한다')]
    public function test_index_paginates_with_cursor(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 25; $i++) {
            $this->notifyNewAlbum($user, 2000 + $i);
        }

        $first = $this->withSession($this->loginSession($user))->getJson('/api/notifications');
        $first->assertJsonCount(20, 'data.notifications');
        $nextCursor = $first->json('data.next_cursor');
        $this->assertNotNull($nextCursor);

        $second = $this->withSession($this->loginSession($user))->getJson('/api/notifications?cursor=' . urlencode($nextCursor));
        $second->assertJsonCount(5, 'data.notifications');
        $second->assertJsonPath('data.next_cursor', null);

        $ids = array_merge(
            array_column($first->json('data.notifications'), 'id'),
            array_column($second->json('data.notifications'), 'id'),
        );
        $this->assertCount(25, array_unique($ids));
    }

    #[TestDox('다른 유저의 알림은 읽음 처리할 수 없다')]
    public function test_mark_read_rejects_other_users_notification(): void
    {
        $owner = User::factory()->create();
        $this->notifyNewAlbum($owner);
        $notification = $owner->notifications()->first();

        $response = $this->withSession($this->loginSession(User::factory()->create()))
            ->postJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(403);
        $this->assertNull($notification->fresh()->read_at);
    }

    #[TestDox('본인의 알림을 읽음 처리한다')]
    public function test_mark_read_marks_own_notification(): void
    {
        $user = User::factory()->create();
        $this->notifyNewAlbum($user);
        $notification = $user->notifications()->first();

        $response = $this->withSession($this->loginSession($user))
            ->postJson("/api/notifications/{$notification->id}/read");

        $response->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[TestDox('모두 읽음 처리는 본인의 알림에만 적용된다')]
    public function test_mark_all_read_only_affects_own_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->notifyNewAlbum($user);
        $this->notifyNewAlbum($otherUser, 1002);

        $response = $this->withSession($this->loginSession($user))->postJson('/api/notifications/read-all');

        $response->assertOk();
        $this->assertSame(0, $user->unreadNotifications()->count());
        $this->assertSame(1, $otherUser->unreadNotifications()->count());
    }

    #[TestDox('읽은 알림 삭제는 본인의 읽은 알림만 삭제한다')]
    public function test_delete_read_only_deletes_own_read_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->notifyNewAlbum($user);
        $this->notifyNewAlbum($user, 1002);
        $this->notifyNewAlbum($otherUser, 1003);
        $user->notifications()->latest()->first()->markAsRead();
        $otherUser->notifications()->first()->markAsRead();

        $response = $this->withSession($this->loginSession($user))->deleteJson('/api/notifications/read');

        $response->assertOk();
        $this->assertSame(1, $user->notifications()->count());
        $this->assertSame(1, $user->unreadNotifications()->count());
        $this->assertSame(1, $otherUser->notifications()->count());
    }
}
