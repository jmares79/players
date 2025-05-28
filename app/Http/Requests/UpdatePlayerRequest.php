<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
{
    public function rules(): array
    {
//        dd('UPDATE REQUEST', $this->all());
        return [
            'name' => 'bail|required|string|max:255',
            'position' => 'bail|required|string|max:255|in:defender,midfielder,forward',
            'playerSkills' => 'bail|array|sometimes|min:1|max:5',
            'playerSkills.*.skill' => 'bail|required|exists:skills,name',
            'playerSkills.*.value' => 'bail|required|numeric|min:1|max:100',
        ];
    }
}
