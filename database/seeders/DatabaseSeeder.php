<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Admin;
use App\Models\File;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        for ($i = 0; $i < 10; $i++) {
            User::factory()->create([
                'userName' => fake()->name(),
                'number' => fake()->phoneNumber(),
                'password' => Hash::make('password'),
            ]);
            $teacher = Teacher::factory()->create([
                'name' => fake()->name(),
                'userName' => fake()->name(),
                'number' => fake()->phoneNumber(),
                'password' => Hash::make('password'),
            ]);

            Admin::factory()->create([
                'name' => $teacher->name,
                'userName' => $teacher->userName,
                'password' => $teacher->password,
                'teacher_id' => $teacher->id,
                'privileges' => 0,
            ]);
            $subject = Subject::factory()->create([
                'name' => fake()->colorName(),
                'lectures'=>0,
                'subscriptions'=>0,
            ]);
            Admin::factory()->create([
                'name' => fake()->name(),
                'userName' => fake()->name(),
                'password' => Hash::make('password'),
                'privileges' => rand(1, 2),
                'teacher_id' => null,
            ]);
            $file = File::factory()->create([
                'name'=>fake()->name(),
                'type'=>('PDF'),
                'description'=>fake()->text(),
                'subject_id' => rand(1, Subject::count()),
            ]);
            $teacher->subjects()->attach($subject->id);
            $subject = Subject::where('id', $file->subject_id)->first();
            $subject->lectures+=1;
            $subject->save();
        }
    }
}
