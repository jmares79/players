<?php

namespace Tests\Feature\Player;

use App\Player\Models\Player;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlayerUpdateTest extends TestCase
{
    use RefreshDatabase;

    /* @see \App\Player\Controllers\PlayerController::update() */
    protected string $route = 'player.update';

    protected array $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => SkillSeeder::class]);
    }

    #[Test]
    public function it_cannot_update_player_on_invalid_payload()
    {
        $player = Player::factory()->create(['name' => 'Player 1', 'position' => 'defender']);
        $player->skills()->attach(1, ['value' => 50]);

        $this->json('PUT', route($this->route, ['player' => $player->id]), [], $this->headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'position']);

        $payload = [
            'name' => 'Super cool player',
            'position' => 'invalid_position',
            'playerSkills' => [
                ['skill' => 'invalid', 'value' => 75],
                ['skill' => 'speed', 'value' => 60],
            ],
        ];

        $this->json('PUT', route($this->route, ['player' => $player->id]), $payload, $this->headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['position', 'playerSkills.0.skill']);
    }

    #[Test]
    public function it_can_update_player()
    {
        $player = Player::factory()->create(['name' => 'Player 1', 'position' => 'defender']);

        $payload = [
            'name' => 'Player 1',
            'position' => 'midfielder',
            'playerSkills' => [
                ['skill' => 'defense', 'value' => 80],
                ['skill' => 'speed', 'value' => 70],
            ],
        ];

        $this->json('PUT', route($this->route, ['player' => $player->id]), $payload, $this->headers)
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'position',
                'playerSkills' => [
                    '*' => [
                        'name',
                        'value',
                    ],
                ],
            ])
            ->assertJsonFragment(['name' => $payload['name'], 'position' => $payload['position']])
            ->assertJsonCount(2, 'playerSkills')
            ->assertJsonFragment(['name' => 'defense', 'value' => 80])
            ->assertJsonFragment(['name' => 'speed', 'value' => 70]);
    }
}
