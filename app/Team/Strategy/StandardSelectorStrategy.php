<?php

namespace App\Team\Strategy;

use App\Player\Models\Player;
use App\Team\Interfaces\TeamSelectorInterface;

class StandardSelectorStrategy implements TeamSelectorInterface
{
    /**
     * Implements the set of rules in order to select the best players based on the given requirements.
     *
     * @param array $requirements An array in the shape of: <position, mainSkill, numberOfPlayers>
     * @return array An array of players that match the requirements.
     */
    public function select(array $requirements): array
    {
//        dd(Player::all());
        $players = [];

        foreach ($requirements as $requirement) {
            // Rule 1: Given a position & a skill, select the players that match both criteria when enough DB data.
            $players = array_merge($players, $this->fetchPlayers(
                $requirement['position'],
                $requirement['mainSkill'],
                (int)$requirement['numberOfPlayers']
            ));

//            if (count($players) < $numberOfPlayers) {
//                return []; // Not enough players found
//            }

//            return array_slice($players, 0, $numberOfPlayers);
        }

//        dd($players);
        return $this->formatResult($players);
    }

    protected function fetchPlayers(string $position, string $skill, int $numberOfPlayers): array
    {
        return Player::where('position', $position)
            ->with('skills')
            ->whereHas('skills', function ($query) use ($skill) {
                $query->where('name', $skill);
            })
            ->get()
            ->map(function ($player) use ($skill) {
                $skill = $player->skills->first();
                return [
                    'player' => $player->toArray(),
                    'value' => $skill->pivot->value,
                ];
            })
            ->sortByDesc('value')
            ->take($numberOfPlayers)
            ->values()
            ->toArray();
    }

    protected function formatResult(array $players): array
    {
        return array_map(function ($player) {
            return [
                'id' => $player['player']['id'],
                'name' => $player['player']['name'],
                'position' => $player['player']['position'],
                'skills' => array_map(function ($skill) {
                    return [
                        'name' => $skill['name'],
                        'value' => $skill['pivot']['value'],
                    ];
                }, $player['player']['skills']),
            ];
        }, $players);
    }
}
