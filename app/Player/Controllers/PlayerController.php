<?php

namespace App\Player\Controllers;

use App\Http\Controllers\Controller;
use App\Player\Logic\PlayerLogic;
use App\Player\Models\Player;
use App\Player\Requests\StorePlayerRequest;
use App\Player\Requests\UpdatePlayerRequest;
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
