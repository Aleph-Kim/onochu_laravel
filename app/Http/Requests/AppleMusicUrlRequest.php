<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppleMusicUrlRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['required', 'string'],
        ];
    }
}
