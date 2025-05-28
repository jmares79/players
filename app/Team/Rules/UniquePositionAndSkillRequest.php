<?php

namespace App\Team\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePositionAndSkillRequest implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $condition = [];

        foreach ($value as $item) {
            if (! isset($item['position']) || ! isset($item['mainSkill'])) {
                $fail("The $attribute must contain both position and skill.");
                return;
            }

            if (array_key_exists($item['position'], $condition) && $condition[$item['position']] == $item['mainSkill']) {
                $fail("The $attribute must be unique.");
                return;
            }

            $condition[$item['position']] = $item['mainSkill'];
        }
    }
}
