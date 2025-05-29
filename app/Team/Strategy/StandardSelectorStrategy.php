<?php

namespace App\Team\Strategy;

use App\Player\Models\Player;
use App\Team\Interfaces\TeamSelectorInterface;

class StandardSelectorStrategy implements TeamSelectorInterface
{
    protected array $selectedIds = [];
    /**
     * Implements the set of rules in order to select the best players based on the given requirements.
     *
     * @param array $requirements An array in the shape of: <position, mainSkill, numberOfPlayers>
     * @return array An array of players that match the requirements.
     */
    public function select(array $requirements): array
    {
        $players = collect();

        foreach ($requirements as $requirement) {
            // Rule 1: Given a position & a skill, select the players that match both criteria when enough DB data.
            $players = $players->merge($this->fetchPlayers(
                $requirement['position'],
                $requirement['mainSkill'],
                (int)$requirement['numberOfPlayers']
            ));

            if ($players->isEmpty()) {
                // Rule 4: fallback to highest ANY skill for position
                $players = $players->merge($this->fetchFallbackPlayers(
                    $requirement['position'],
                    $requirement['mainSkill'],
                    (int)$requirement['numberOfPlayers'],
                ));
            }

            $this->selectedIds[] = $players->pluck('player.id')->toArray();
        }

//        dd($players->toArray());
        return $this->formatResult($players->toArray());
    }

    protected function fetchPlayers(string $position, string $skill, int $numberOfPlayers)
    {
        // Fetch players by position and skill.
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
            ->values();
    }

    protected function fetchFallbackPlayers(string $position, string $skill, int $numberOfPlayers)
    {
        return Player::where('position', $position)
            ->with('skills')
            ->whereNotIn('id', $this->selectedIds)
            ->get()
            ->map(function ($player) use ($skill) {
                $maxValue = $player->skills->max(function ($skill) {
                    return $skill->pivot->value;
                });
                return [
                    'player' => $player->toArray(),
                    'value' => $maxValue,
                ];
            })
            ->sortByDesc('value')
            ->take($numberOfPlayers)
            ->values();
    }
    /**
     * Formats the result to match the expected output structure.
     *
     * @param array $players
     * @return array
     */
    protected function formatResult(array $players): array
    {
//        dd($players);
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
