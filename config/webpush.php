<?php

return [
    /**
     * VAPID 인증 키. `Minishlink\WebPush\VAPID::createVapidKeys()`로 생성한다.
     * 키가 바뀌면 기존 구독이 모두 무효화되므로 한 번 정하면 유지한다.
     */
    'subject'     => env('VAPID_SUBJECT'),
    'public_key'  => env('VAPID_PUBLIC_KEY'),
    'private_key' => env('VAPID_PRIVATE_KEY'),
];
