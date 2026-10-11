<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecommendStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // 추천할 곡의 FLO ID
            'id' => ['required', 'integer'],
            // 별점 (1~5)
            'score' => ['nullable', 'integer', 'between:1,5'],
            // 코멘트
            'comment' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'score' => '별점',
            'comment' => '코멘트',
        ];
    }
}
