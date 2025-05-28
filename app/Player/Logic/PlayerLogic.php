<?php

namespace App\Player\Logic;

use App\Player\Models\Player;
use App\Skill\Models\Skill;

class PlayerLogic
{
    public function create(array $playerPayload): Player
    {
        $player = Player::create($playerPayload);

        foreach ($playerPayload['playerSkills'] as $playerSkill) {
            $skill = Skill::where('name', $playerSkill['skill'])->first();

            if (!$skill) {
                continue;
            }

            $player->skills()->attach($skill->id, ['value' => $playerSkill['value']]);
        }

        return $player;
    }

    public function update(Player $player, array $playerPayload): Player
    {
        $player->update($playerPayload);

        // Detach all skills first
        $player->skills()->detach();

        foreach ($playerPayload['playerSkills'] as $playerSkill) {
            $skill = Skill::where('name', $playerSkill['skill'])->first();

            if (!$skill) {
                continue;
            }

            $player->skills()->attach($skill->id, ['value' => $playerSkill['value']]);
        }

        return $player;
    }
}
