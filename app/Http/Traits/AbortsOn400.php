<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Validation\Validator;

trait AbortsOn400
{
    protected function failedValidation(Validator $validator)
    {
        abort(400);
    }
}
