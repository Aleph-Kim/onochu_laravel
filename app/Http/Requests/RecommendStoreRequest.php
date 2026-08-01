<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecommendStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'score'   => ['nullable', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'score'   => '별점',
            'comment' => '코멘트',
        ];
    }
}
