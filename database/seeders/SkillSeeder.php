<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['defense', 'attack', 'speed', 'stamina', 'strength'] as $skill) {
            \App\Models\Skill::create([
                'name' => $skill,
            ]);
        }
    }
}
