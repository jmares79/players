<?php

namespace App\Logic;

use App\Models\Player;
use App\Models\Skill;

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
