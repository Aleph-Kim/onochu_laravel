<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\User;
use App\Services\WebPushService;
use App\WebPush\NewAlbumPayload;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        if (!session('user')) {
            return response()->json(['code' => 401, 'message' => '로그인이 필요합니다.']);
        }

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->input('endpoint')],
            [
                'user_id'    => session('user.id'),
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
            ],
        );

        return response()->json(['message' => '알림 구독 완료']);
    }

    public function unsubscribe(Request $request)
    {
        if (!session('user')) {
            return response()->json(['code' => 401, 'message' => '로그인이 필요합니다.']);
        }

        PushSubscription::where('endpoint', $request->input('endpoint'))->delete();

        return response()->json(['message' => '알림 구독 해제 완료']);
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
            return response()->json(['code' => 401, 'message' => '로그인이 필요합니다.']);
        }

        $user = User::find(session('user.id'));

        if ($user->pushSubscriptions()->count() === 0) {
            return response()->json(['code' => 400, 'message' => '먼저 알림을 구독해 주세요.']);
        }

        $webPush->sendToUser($user, NewAlbumPayload::build([
            [
                'title'   => '테스트 앨범',
                'flo_id'  => 0,
                'img_url' => asset('image/logo.png'),
                'artist'  => [['name' => '테스트 아티스트', 'flo_id' => 0]],
            ],
        ]));

        return response()->json(['message' => '테스트 알림을 발송했습니다.']);
    }
}
