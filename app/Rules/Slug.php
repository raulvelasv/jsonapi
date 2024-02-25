<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->hasUnderscores($value)) {
            $fail(__('validation.no_underscores', ['attribute' => 'slug']));
        }

        if ($this->startsWithDashes($value)) {
            $fail(__('validation.no_starting_dashes', ['attribute' => 'slug']));
        }

        if ($this->endsWithDashes($value)) {
            $fail(__('validation.no_ending_dashes', ['attribute' => 'slug']));
        }
    }

    /**
     * @param mixed $value
     * @return false|int
     */
    public function hasUnderscores(mixed $value): int|false
    {
        return preg_match('/_/', $value);
    }

    /**
     * @param mixed $value
     * @return false|int
     */
    public function startsWithDashes(mixed $value): int|false
    {
        return preg_match('/^-/', $value);
    }

    /**
     * @param mixed $value
     * @return false|int
     */
    public function endsWithDashes(mixed $value): int|false
    {
        return preg_match('/-$/', $value);
    }
}
