<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Teacher;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 1; $i++) {
            $subject = Subject::factory()->create([
                'name' => fake()->colorName(),
                'lecturesCount'=>0,
                'subscriptions'=>0,
                'image' => 'Images/Subjects/default.png',
            ]);
            }
        //
    }
}
