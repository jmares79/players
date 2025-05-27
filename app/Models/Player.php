<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'position',
    ];
    protected $casts = [
        'playerSkills' => 'array',
    ];

    public function skills(): BelongsToMany
    {
        return $this->BelongsToMany(Skill::class, 'player_skill')
            ->withPivot('value');
    }
}
