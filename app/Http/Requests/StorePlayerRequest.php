<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function rules(): array
    {
//        dd($this->all());
        return [
            'name' => 'bail|required|string|max:255',
            'position' => 'bail|required|string|max:255|in:defender,midfielder,forward',
            'playerSkills' => 'bail|array|required|min:1',
            'playerSkills.*.skill' => 'bail|required|exists:skills,name',
            'playerSkills.*.value' => 'bail|required|numeric',
        ];
    }
}
