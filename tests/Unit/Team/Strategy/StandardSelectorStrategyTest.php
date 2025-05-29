<?php

namespace Tests\Unit\Team\Strategy;

use App\Player\Models\Player;
use App\Skill\Models\Skill;
use App\Team\Strategy\StandardSelectorStrategy;
use Database\Seeders\SkillSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Unit\data\PlayerData;

class StandardSelectorStrategyTest extends TestCase
{
    use RefreshDatabase;
    use PlayerData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => SkillSeeder::class]);
    }

    #[Test]
    public function it_can_select_players_that_exists_with_position_and_skill()
    {
        // Seed the DB with the necessary data compatible with the payload -- Rule 1
        // => At least the amount of players compatible with the payload with no fallbacks
        $requirements = $this->generateCompletePlayersDataAndRequest();

        $selector = new StandardSelectorStrategy;
        $response = $selector->select($requirements);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertCount(3, $response);

        $this->assertArrayHasKey('name', $response[0]);
        $this->assertArrayHasKey('position', $response[0]);
        $this->assertArrayHasKey('skills', $response[0]);

        // 1 midfielder & At least one of the player's skills should match the skill requirement
        $this->assertEquals($requirements[0]['position'], $response[0]['position']);
        $skillRequirement = $requirements[0]['mainSkill'];

        $skillNames = array_intersect([$skillRequirement], array_map(function ($skill) {
            return $skill['name'];
        }, $response[0]['skills']));
        $this->assertNotEmpty($skillNames);

        $responseValue = array_values(array_filter($response[0]['skills'], function ($skill) use ($skillRequirement) {
            return $skill['name'] === $skillRequirement;
        }));

        $this->assertEquals(94, $responseValue[0]['value']);

        // 2 defenders
        $this->assertEquals($requirements[1]['position'], $response[1]['position']);
        $this->assertEquals($requirements[1]['position'], $response[2]['position']);
    }
}
