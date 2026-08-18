<?php

namespace App\Notifications;

use App\Models\NewAlbum;
use Illuminate\Notifications\Notification;

class NewAlbumNotification extends Notification
{
    public function __construct(private NewAlbum $newAlbum)
    {
    }

    /**
     * 웹 푸시는 WebPushService에서 별도 발송하므로 헤더 알림 탭용 DB 채널만 사용
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $artistName = $this->newAlbum->artists->first()?->artist_name ?? '';

        // new_album_flo_id: 앨범 상세 진입 시 이 값으로 알림을 찾아 읽음 처리
        return [
            'label' => '신곡',
            'title' => trim("{$artistName} - {$this->newAlbum->album_title}", ' -'),
            'img_url' => $this->newAlbum->album_img_url,
            'url' => route('album.detail', ['id' => $this->newAlbum->flo_id, 'new_album' => 1]),
            'new_album_flo_id' => $this->newAlbum->flo_id,
        ];
    }
}
