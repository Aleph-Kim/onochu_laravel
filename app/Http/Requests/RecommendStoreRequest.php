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
            // 별점 (0.5~5, 0.5 단위)
            'score' => ['nullable', 'numeric', 'between:0.5,5', 'multiple_of:0.5'],
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
