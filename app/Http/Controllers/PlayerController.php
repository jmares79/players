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
            Player::all()->toResourceCollection(),
            Response::HTTP_OK
        );
    }

    public function store(StorePlayerRequest $request)
    {
        $player = $this->playerLogic->create($request->validated());

        return response()->json($player->toResource(), Response::HTTP_CREATED, [
            'Location' => route('player.show', $player->id),
        ]);
    }

    public function show(Player $player)
    {
        return response()->json([$player], Response::HTTP_OK);
    }

    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $player = $this->playerLogic->update($player, $request->validated());

        return response()->json($player->toResource(), Response::HTTP_OK, [
            'Location' => route('player.show', $player->id),
        ]);
    }

    public function destroy(Player $player)
    {
        //
    }
}
