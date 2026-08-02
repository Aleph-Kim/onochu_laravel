<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SongDetailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }
}
