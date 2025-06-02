<?php

namespace Tests\Unit\Team\Strategy;

use App\Team\Exceptions\InsufficientAmountOfPlayersException;
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
        $this->assertArrayHasKey('playerSkills', $response[0]);

        // 1 midfielder & At least one of the player's skills should match the skill requirement
        $this->assertEquals($requirements[0]['position'], $response[0]['position']);
        $skillRequirement = $requirements[0]['mainSkill'];

        $skillNames = array_intersect([$skillRequirement], array_map(function ($skill) {
            return $skill['skill'];
        }, $response[0]['playerSkills']));
        $this->assertNotEmpty($skillNames);

        $responseValue = array_values(array_filter($response[0]['playerSkills'], function ($skill) use ($skillRequirement) {
            return $skill['skill'] === $skillRequirement;
        }));

        $this->assertEquals(94, $responseValue[0]['value']);

        // 2 defenders
        $this->assertEquals($requirements[1]['position'], $response[1]['position']);
        $this->assertEquals($requirements[1]['position'], $response[2]['position']);
    }

    #[Test]
    public function it_can_select_players_with_fallback_skill()
    {
        $requirements = $this->generateFallbackSkillsDataAndRequest();

        $selector = new StandardSelectorStrategy;
        $response = $selector->select($requirements);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);

        $this->assertArrayHasKey('name', $response[0]);
        $this->assertArrayHasKey('position', $response[0]);
        $this->assertArrayHasKey('playerSkills', $response[0]);

        $this->assertEquals($requirements[0]['position'], $response[0]['position']);

        // Skill isn't the requested one, but a different one with max value
        $this->assertNotEquals($requirements[0]['mainSkill'], $response[0]['playerSkills'][0]['skill']);
        $this->assertEquals(93, $response[0]['playerSkills'][0]['value']);
    }

    #[Test]
    public function it_can_select_players_with_fallback_multiple_skills()
    {
        $requirements = $this->generateFallbackMultipleSkillsDataAndRequest();

        $selector = new StandardSelectorStrategy;
        $response = $selector->select($requirements);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertArrayHasKey('playerSkills', $response[0]);
        $this->assertNotEmpty($response[0]['playerSkills']);

        $this->assertCount(1, $response);
        $this->assertEquals('Player 1 defender', $response[0]['name']);
        $this->assertEquals($requirements[0]['position'], $response[0]['position']);

        $maxValue = max(array_column($response[0]['playerSkills'], 'value'));
        $this->assertEquals(100, $maxValue);

        // When trying with the same request for asking 2 players, it should return P1 with 100 speed and P3 with 93 stamina
        $requirements[0]['numberOfPlayers'] = 2;

        $selector = new StandardSelectorStrategy;
        $response = $selector->select($requirements);

        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        // First player is P1
        $this->assertEquals('Player 1 defender', $response[0]['name']);
        $this->assertEquals($requirements[0]['position'], $response[0]['position']);
        $this->assertArrayHasKey('playerSkills', $response[0]);

        $maxValue = max(array_column($response[0]['playerSkills'], 'value'));
        $this->assertEquals(100, $maxValue);

        // First player is P3
        $this->assertArrayHasKey('playerSkills', $response[1]);
        $this->assertEquals('Player 3 defender', $response[1]['name']);
        $this->assertEquals($requirements[0]['position'], $response[1]['position']);

        $maxValue = max(array_column($response[1]['playerSkills'], 'value'));
        $this->assertEquals(93, $maxValue);
    }

    #[Test]
    public function it_throws_exception_when_no_positions_available()
    {
        $requirements = $this->generateFallbackPositionsDataAndRequest();

        $this->expectException(InsufficientAmountOfPlayersException::class);

        $selector = new StandardSelectorStrategy;
        $selector->select($requirements);
    }
}
