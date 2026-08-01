<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushUnsubscribeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'endpoint' => '구독 엔드포인트',
        ];
    }
}
