<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Logic\PlayerLogic;
use App\Models\Player;
use Symfony\Component\HttpFoundation\Response;

class PlayerController extends Controller
{
    public function __construct(protected readonly PlayerLogic $playerLogic) {}
    public function index()
    {
        return response()->json(
            Player::all(),
            Response::HTTP_OK
        );
    }

    public function store(StorePlayerRequest $request)
    {
        $player = $this->playerLogic->create($request->validated());

        return response()->json($player->toResource(), Response::HTTP_CREATED, [
            'Location' => route('players.show', $player->id),
        ]);
    }

    public function show(Player $player)
    {
        return response()->json([$player], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlayerRequest $request, Player $player)
    {
        dd('Update Player: ' . $player->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        //
    }
}
