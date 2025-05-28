<?php

namespace App\Skill\Models;

use App\Player\Models\Player;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_skill');
    }
}
