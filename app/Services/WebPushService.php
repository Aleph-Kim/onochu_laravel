<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    private WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('webpush.subject'),
                'publicKey'  => config('webpush.public_key'),
                'privateKey' => config('webpush.private_key'),
            ],
        ]);
    }

    // 발송 큐에 추가 (실제 전송은 flush에서)
    public function queue(PushSubscription $subscription, array $payload): void
    {
        $this->webPush->queueNotification(
            Subscription::create([
                'endpoint' => $subscription->endpoint,
                'keys'     => [
                    'p256dh' => $subscription->public_key,
                    'auth'   => $subscription->auth_token,
                ],
            ]),
            json_encode($payload),
        );
    }

    // 큐에 쌓인 알림을 일괄 전송. 만료된(410/404) 구독은 자동 삭제
    public function flush(): void
    {
        foreach ($this->webPush->flush() as $report) {
            if ($report->isSubscriptionExpired()) {
                PushSubscription::where('endpoint', $report->getEndpoint())->delete();
            }
        }
    }

    // 한 유저의 모든 구독 기기로 즉시 발송
    public function sendToUser(User $user, array $payload): void
    {
        foreach ($user->pushSubscriptions as $subscription) {
            $this->queue($subscription, $payload);
        }

        $this->flush();
    }
}
