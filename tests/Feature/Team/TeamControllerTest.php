<?php

namespace Tests\Feature\Team;

use App\Team\Controllers\TeamController;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase;

    /* @see TeamController */
    protected string $route = 'team.process';

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
    public function it_cannot_process_team_on_invalid_payload()
    {
        $this->post(route($this->route), [], $this->headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['conditions']);

        $payload = [
            [
                'position' => 'invalid',
                'mainSkill' => 'skill1',
                'numberOfPlayers' => 'foo'
            ],
        ];
        $this->json('POST', route($this->route), $payload, $this->headers)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['conditions.0.position', 'conditions.0.mainSkill', 'conditions.0.numberOfPlayers']);
    }

    #[Test]
    public function it_can_process_team()
    {
        $payload = [
            [
                'position' => 'midfielder',
                'mainSkill' => 'speed',
                'numberOfPlayers' => 1
            ],
            [
                'position' => 'defender',
                'mainSkill' => 'strength',
                'numberOfPlayers' => 2
            ],
        ];

//        $this->json('POST', route($this->route), $payload, $this->headers)->dump()
//            ->assertStatus(200)
//            ->assertExactJson([
//                '*' => [
//                    'position',
//                    'mainSkill',
//                    'players'
//                ]
//            ]);
    }
}
