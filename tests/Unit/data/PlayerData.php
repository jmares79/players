<?php

namespace Tests\Unit\data;

use App\Player\Models\Player;
use App\Skill\Models\Skill;

trait PlayerData
{
    /**
     * Generates 4 players, 2 midfielders to fetch only 1 with MAX,
     * and 2 defenders that should BOTH be selected the requirement query
     * for a team selection without falling back in further rules.
     *
     * @return array[]
     */
    public function generateCompletePlayersDataAndRequest(): array
    {
        $player1 = Player::factory()->create([
            'name' => 'Player 1 midfielder',
            'position' => 'midfielder',
        ]);

        $player1->skills()->attach(Skill::whereName('defense')->first()->id, ['value' => 61]);
        $player1->skills()->attach(Skill::whereName('speed')->first()->id, ['value' => 91]);
        $player1->skills()->attach(Skill::whereName('stamina')->first()->id, ['value' => 81]);

        $player2 = Player::factory()->create([
            'name' => 'Player 2 defender',
            'position' => 'defender',
        ]);
        $player2->skills()->attach(Skill::whereName('strength')->first()->id, ['value' => 82]);
        $player2->skills()->attach(Skill::whereName('attack')->first()->id, ['value' => 32]);
        $player2->skills()->attach(Skill::whereName('defense')->first()->id, ['value' => 82]);

        $player3 = Player::factory()->create([
            'name' => 'Player 3 defender',
            'position' => 'defender',
        ]);
        $player3->skills()->attach(Skill::whereName('strength')->first()->id, ['value' => 73]);
        $player3->skills()->attach(Skill::whereName('attack')->first()->id, ['value' => 43]);
        $player3->skills()->attach(Skill::whereName('defense')->first()->id, ['value' => 83]);

        $player4 = Player::factory()->create([
            'name' => 'Player 4 midfielder',
            'position' => 'midfielder',
        ]);

        $player4->skills()->attach(Skill::whereName('defense')->first()->id, ['value' => 64]);
        $player4->skills()->attach(Skill::whereName('speed')->first()->id, ['value' => 94]);
        $player4->skills()->attach(Skill::whereName('stamina')->first()->id, ['value' => 84]);

        // Compatible set of requirements to match the players created above
        return [
            [
                'position' => 'midfielder',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ],
            [
                'position' => 'defender',
                'mainSkill' => 'strength',
                'numberOfPlayers' => 2
            ],
        ];
    }

    /**
     * Generates players and skill data to perform a fallback response on the midfielders and defenders request
     *
     * @return array
     */
    public function generateFallbackSkillsDataAndRequest(): array
    {
        Player::factory()
            ->create([
                'name' => 'Player 1 defender',
                'position' => 'defender',
            ])
            ->skills()
            ->attach(Skill::whereName('speed')->first()->id, ['value' => 91]);

        Player::factory()
            ->create([
                'name' => 'Player 2 defender',
                'position' => 'defender',
            ])
            ->skills()
            ->attach(Skill::whereName('strength')->first()->id, ['value' => 22]);

        Player::factory()
            ->create([
                'name' => 'Player 3 defender',
                'position' => 'defender',
            ])
            ->skills()
            ->attach(Skill::whereName('stamina')->first()->id, ['value' => 93]);

        return [
            [
                'position' => 'defender',
                'mainSkill' => 'defense',
                'numberOfPlayers' => 1
            ],
        ];
    }
}
