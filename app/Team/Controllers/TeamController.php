<?php

namespace App\Team\Controllers;

use App\Http\Controllers\Controller;
use App\Team\Interfaces\TeamSelectorInterface;
use App\Team\Requests\TeamSelectionRequest;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    public function __invoke(TeamSelectionRequest $request, TeamSelectorInterface $teamSelector): JsonResponse
    {
        $teams = $teamSelector->select($request->validated());

        return response()->json($teams, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
