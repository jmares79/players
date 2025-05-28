<?php

namespace Database\Seeders;

use App\Skill\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['defense', 'attack', 'speed', 'stamina', 'strength'] as $skill) {
            Skill::create([
                'name' => $skill,
            ]);
        }
    }
}
