<?php

namespace App\Console\Commands;

use App\Models\NewAlbum;
use App\Models\User;
use App\Services\WebPushService;
use App\WebPush\NewAlbumPayload;
use Illuminate\Console\Command;

class TestNewAlbumNotification extends Command
{
    protected $signature   = 'notify:test-new-album {user : 알림을 받을 유저 ID}';
    protected $description = '가장 최근에 추가된 신곡 앨범을 기준으로 지정한 유저에게 테스트 웹 푸시 발송';

    public function __construct(
        private WebPushService $webPush,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $user = User::find($this->argument('user'));
        if (!$user) {
            $this->error('유저를 찾을 수 없습니다.');
            return self::FAILURE;
        }

        if ($user->pushSubscriptions()->count() === 0) {
            $this->error('해당 유저는 알림을 구독하고 있지 않습니다.');
            return self::FAILURE;
        }

        $newAlbum = NewAlbum::with('artists')->latest()->first();
        if (!$newAlbum) {
            $this->error('new_albums 테이블에 데이터가 없습니다.');
            return self::FAILURE;
        }

        $payload = NewAlbumPayload::build([
            [
                'title'   => $newAlbum->album_title,
                'flo_id'  => $newAlbum->flo_id,
                'img_url' => $newAlbum->album_img_url,
                'artist'  => $newAlbum->artists->map(fn ($artist) => [
                    'name'    => $artist->artist_name,
                    'flo_id'  => $artist->flo_id,
                ])->all(),
            ],
        ]);

        $this->webPush->sendToUser($user, $payload);

        $this->info("{$user->nickname}(id={$user->id})에게 테스트 알림을 발송했습니다: {$newAlbum->album_title}");
        return self::SUCCESS;
    }
}
