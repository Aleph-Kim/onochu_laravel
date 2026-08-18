<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        $user = User::findOrFail(session('user.id'));

        $notifications = $user->notifications()->latest()->limit(20)->get();

        $items = $notifications->map(fn($notification) => [
            'id' => $notification->id,
            'label' => $notification->data['label'] ?? null,
            'title' => $notification->data['title'] ?? '',
            'img_url' => $notification->data['img_url'] ?? null,
            'url' => $notification->data['url'] ?? route('main'),
            'unread' => is_null($notification->read_at),
        ]);

        return $this->successResponse('알림 목록', [
            'notifications' => $items,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(DatabaseNotification $notification)
    {
        // 타 유저의 알림을 읽음 처리하지 못하도록 소유자 검증
        if (!session('user')
            || $notification->notifiable_type !== User::class
            || $notification->notifiable_id != session('user.id')) {
            return $this->errorResponse('권한이 없습니다.', 403);
        }

        $notification->markAsRead();

        return $this->successResponse('읽음 처리되었습니다.');
    }

    public function markAllRead()
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', session('user.id'))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse('모두 읽음 처리되었습니다.');
    }
}
