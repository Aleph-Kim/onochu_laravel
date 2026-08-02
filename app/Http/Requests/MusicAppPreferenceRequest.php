<?php

namespace App\Http\Requests;

use App\Enums\MusicApp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MusicAppPreferenceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'app' => ['required', Rule::enum(MusicApp::class)],
        ];
    }
}
