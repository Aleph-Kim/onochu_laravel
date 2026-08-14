<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AbortsOnValidationFailure;
use Illuminate\Foundation\Http\FormRequest;

class RedirectYoutubeRequest extends FormRequest
{
    use AbortsOnValidationFailure;

    public function rules(): array
    {
        return [
            'q' => ['required', 'string'],
        ];
    }
}
