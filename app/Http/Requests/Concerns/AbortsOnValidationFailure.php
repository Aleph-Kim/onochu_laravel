<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;

trait AbortsOnValidationFailure
{
    // 기본 리다이렉트/422 응답 대신 400 에러 페이지 노출
    protected function failedValidation(Validator $validator): void
    {
        abort(400);
    }
}
