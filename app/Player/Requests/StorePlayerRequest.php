<?php

namespace App\Player\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'bail|required|string|max:255',
            'position' => 'required|string|max:255|in:'.implode(',', config('positions.positions')),
            'playerSkills' => 'array|required|min:1|max:5',
            'playerSkills.*.skill' => 'required|exists:skills,name',
            'playerSkills.*.value' => 'required|numeric|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The player name is required.',
            'name.string' => 'The player name must be a string.',
            'name.max' => 'The player name may not be greater than 255 characters.',
            'position.required' => 'The player position is required.',
            'playerSkills.required' => 'At least one skill is required for the player.',
            'playerSkills.array' => 'The player skills must be an array.',
            'playerSkills.min' => 'The player must have at least one skill.',
            'playerSkills.max' => 'The player can have a maximum of five skills.',
            'playerSkills.*.skill.required' => 'Each skill must have a name.',
            'playerSkills.*.skill.exists' => 'The selected skill is invalid.',
        ];
    }
}
