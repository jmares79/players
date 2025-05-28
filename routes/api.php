<?php

use App\Player\Controllers\PlayerController;
use App\Team\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('player', PlayerController::class)
    ->only(['index', 'show', 'store', 'update']);

Route::delete('player/{player}', [PlayerController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('player.destroy');

Route::post('team/process', TeamController::class)->name('team.process');
