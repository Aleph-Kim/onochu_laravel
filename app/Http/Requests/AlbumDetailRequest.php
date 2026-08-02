<?php

namespace App\Http\Requests;

use App\Http\Traits\AbortsOn400;
use Illuminate\Foundation\Http\FormRequest;

class AlbumDetailRequest extends FormRequest
{
    use AbortsOn400;

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'new_album' => ['nullable', 'boolean'],
        ];
    }
}
