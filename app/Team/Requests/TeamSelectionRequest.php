<?php

namespace App\Team\Requests;

use App\Team\Rules\UniquePositionAndSkillRequest;
use Illuminate\Foundation\Http\FormRequest;

class TeamSelectionRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $this->merge(['conditions' => $this->all()]);
    }

    public function rules(): array
    {
        return [
            'conditions' => ['bail', 'required', 'array', 'min:1', new UniquePositionAndSkillRequest()],
            'conditions.*.position' => 'required|string|in:'.implode(',', config('positions.positions')),
            'conditions.*.mainSkill' => 'required|string|min:1|exists:skills,name',
            'conditions.*.numberOfPlayers' => 'required|integer',
        ];
    }
}
