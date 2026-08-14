<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AbortsOnValidationFailure;
use Illuminate\Foundation\Http\FormRequest;

class RecommendCreateRequest extends FormRequest
{
    use AbortsOnValidationFailure;

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}
