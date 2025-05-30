<?php

namespace App\Team\Strategy;

use App\Player\Models\Player;
use App\Team\Exceptions\InsufficientAmountOfPlayersException;
use App\Team\Interfaces\TeamSelectorInterface;

class StandardSelectorStrategy implements TeamSelectorInterface
{
    protected array $selectedIds = [];

    /**
     * Implements the set of rules in order to select the best players based on the given requirements.
     *
     * @param array $requirements An array in the shape of: <position, mainSkill, numberOfPlayers>
     * @return array An array of players that match the requirements.
     * @throws InsufficientAmountOfPlayersException
     */
    public function select(array $requirements): array
    {
        $players = collect();

        foreach ($requirements as $requirement) {
            $requiredNumberOfPlayers = (int) $requirement['numberOfPlayers'];

            // If the amount of players for a specific position is less than the required amount, return an error.
            if (Player::where('position', $requirement['position'])->count() < $requiredNumberOfPlayers) {
                throw new InsufficientAmountOfPlayersException("Insufficient amount of players for position: {$requirement['position']}");
            }

            // Rule 1: Given a position & a skill, select the players that match both criteria when enough DB data.
            $players = $players->merge($this->fetchPlayers(
                $requirement['position'],
                $requirement['mainSkill'],
                $requiredNumberOfPlayers
            ));

            if ($players->isEmpty()) {
                // Rule 4: fallback to highest ANY skill for position
                $players = $players->merge($this->fetchFallbackSkillsPlayers(
                    $requirement['position'],
                    $requirement['mainSkill'],
                    $requiredNumberOfPlayers,
                ));
            }

            $this->selectedIds[] = $players->pluck('player.id')->toArray();

            if ($players->count() < $requiredNumberOfPlayers) {

            }
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

    protected function fetchFallbackSkillsPlayers(string $position, string $skill, int $numberOfPlayers)
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
        return array_map(function ($player) {
            return [
                'id' => $player['player']['id'],
                'name' => $player['player']['name'],
                'position' => $player['player']['position'],
                'playerSkills' => array_map(function ($skill) {
                    return [
                        'skill' => $skill['name'],
                        'value' => $skill['pivot']['value'],
                    ];
                }, $player['player']['skills']),
            ];
        }, $players);
    }
}
