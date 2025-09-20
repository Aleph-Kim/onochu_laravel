<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function __construct(
        private string $attribute,
        private bool $mobileOnly = false,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = $this->mobileOnly
            ? '/^01[016789]-?\d{3,4}-?\d{4}$/'
            : '/^(?:01[016-9]-?\d{3,4}-?\d{4}|0[2-9]{1,2}-?\d{3,4}-?\d{4}|(?:15|16|18)\d{2}-?\d{4})$/';

        if (!preg_match($pattern, $value)) {
            $fail("$this->attribute 형식이 올바르지 않습니다.");
        }
    }
}
