<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushSubscribeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'endpoint' => '구독 엔드포인트',
            'keys.p256dh' => '암호화 키',
            'keys.auth' => '인증 비밀값',
        ];
    }
}
