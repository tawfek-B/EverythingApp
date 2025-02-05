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
        for ($i = 0; $i < 10; $i++) {
            $subject = Subject::factory()->create([
                'name' => fake()->colorName(),
                'lectures'=>0,
                'subscriptions'=>0,
            ]);
            Teacher::where('id', rand(1, Teacher::count()))->first()->subjects()->attach($subject->id);
            }
        //
    }
}
