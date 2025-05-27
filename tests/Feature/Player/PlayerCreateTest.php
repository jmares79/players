<?php

namespace Feature\Player;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlayerCreateTest extends TestCase
{
    use RefreshDatabase;

    /* @see \App\Http\Controllers\PlayerController::store() */
    protected string $route = 'players.store';
    protected array $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];
    #[Test]
    public function it_cannot_create_player_on_invalid_payload()
    {
        $this->post(route($this->route), [], $this->headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'position', 'playerSkills']);
    }

    #[Test]
    public function it_can_create_player(): void
    {
        $payload = [
            'name' => 'John Doe',
            'position' => 'defender',
            'playerSkills' => [
                ['skill_id' => 1, 'level' => 5],
                ['skill_id' => 2, 'level' => 3],
            ],
        ];

        $this->post(route($this->route, $payload), $this->headers)->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'position',
                    'playerSkills' => [
                        '*' => [
                            'skill_id',
                            'level',
                        ],
                    ],
                ],
            ]);

        $this->assertDatabaseCount(Player::class, 1);
        $this->assertCount(2, Player::first()->playerSkills);
    }
}
