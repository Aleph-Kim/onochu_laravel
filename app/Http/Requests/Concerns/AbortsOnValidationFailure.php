<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;

trait AbortsOnValidationFailure
{
    protected function failedValidation(Validator $validator): void
    {
        abort(400);
    }
}
