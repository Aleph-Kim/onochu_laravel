<?php

namespace App\Http\Controllers;

use App\Http\Requests\PushSubscribeRequest;
use App\Http\Requests\PushUnsubscribeRequest;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\WebPushService;
use App\WebPush\NewAlbumPayload;

class PushSubscriptionController extends Controller
{
    public function subscribe(PushSubscribeRequest $request)
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->validated('endpoint')],
            [
                'user_id' => session('user.id'),
                'public_key' => $request->validated('keys.p256dh'),
                'auth_token' => $request->validated('keys.auth'),
            ],
        );

        return $this->successResponse('알림 구독 완료');
    }

    public function unsubscribe(PushUnsubscribeRequest $request)
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        PushSubscription::where('endpoint', $request->validated('endpoint'))->delete();

        return $this->successResponse('알림 구독 해제 완료');
    }

    /**
     * 로그인한 본인에게 테스트 푸시 발송
     *
     * @param WebPushService $webPush
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(WebPushService $webPush)
    {
        if (!session('user')) {
            return $this->errorResponse('로그인이 필요합니다.', 401);
        }

        $user = User::find(session('user.id'));

        if ($user->pushSubscriptions()->count() === 0) {
            return $this->errorResponse('먼저 알림을 구독해 주세요.', 400);
        }

        $webPush->sendToUser($user, NewAlbumPayload::build([
            [
                'title' => '테스트 앨범',
                'flo_id' => 0,
                'img_url' => asset('image/logo.png'),
                'artist' => [['name' => '테스트 아티스트', 'flo_id' => 0]],
            ],
        ]));

        return $this->successResponse('테스트 알림을 발송했습니다.');
    }
}
