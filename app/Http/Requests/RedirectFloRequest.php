<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedirectFloRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
        ];
    }
}
