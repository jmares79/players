<?php

namespace Tests\Feature\Player;

use App\Models\Player;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlayerCreateTest extends TestCase
{
    use RefreshDatabase;

    /* @see \App\Http\Controllers\PlayerController::store() */
    protected string $route = 'player.store';

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
                ['skill' => 'defense', 'value' => 75],
                ['skill' => 'speed', 'value' => 60],
            ],
        ];

        $this->post(route($this->route,$payload), $this->headers)->assertCreated()
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
            ->assertJsonCount(2, 'playerSkills')
            ->assertJsonFragment([
                'name' => 'defense',
                'value' => 75,
            ]);

        $this->assertDatabaseCount(Player::class, 1);
        $this->assertCount(2, Player::first()->skills);
    }
}
