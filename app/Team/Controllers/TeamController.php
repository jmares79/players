<?php

namespace App\Team\Controllers;

use App\Http\Controllers\Controller;
use App\Team\Exceptions\InsufficientAmountOfPlayersException;
use App\Team\Interfaces\TeamSelectorInterface;
use App\Team\Requests\TeamSelectionRequest;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    public function __invoke(TeamSelectionRequest $request, TeamSelectorInterface $teamSelector): JsonResponse
    {
        try {
            $teams = $teamSelector->select($request->validated('conditions'));
        } catch (InsufficientAmountOfPlayersException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400, [
                'Content-Type' => 'application/json',
            ]);
        }

        return response()->json($teams, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
