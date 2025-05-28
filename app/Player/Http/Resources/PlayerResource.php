<?php

namespace App\Player\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'playerSkills' => $this->skills?->map(function ($skill) {
                return [
                    'name' => $skill->name,
                    'value' => $skill->pivot->value,
                ];
            })->toArray(),
        ];
    }
}
