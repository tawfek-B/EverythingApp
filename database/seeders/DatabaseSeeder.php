<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Admin;
use App\Models\Lecture;

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
            $randomDigits = mt_rand(900000000, 999999999);

            User::factory()->create([
                'userName' => fake()->name(),
                'countryCode' => '+963',
                'number' => $randomDigits,
                'password' => Hash::make('password'),
                'isBanned' => 0,
            ]);
            $randomDigits = mt_rand(900000000, 999999999);
            $teacher = Teacher::factory()->create([
                'name' => fake()->name(),
                'userName' => fake()->name(),
                'countryCode' => '+963',
                'number' => $randomDigits,
                'image' => 'Admins/teacherDefault.png',
                'links' => '{"Facebook": "https://facebook", "Instagram": "https://instagram", "Telegram": "https://telegram", "YouTube":"https://youtube"}',
                'password' => Hash::make('password'),
            ]);

            Admin::factory()->create([
                'name' => $teacher->name,
                'userName' => $teacher->userName,
                'password' => $teacher->password,
                'teacher_id' => $teacher->id,
                'countryCode' => '+963',
                'number' => $teacher->number,
                'privileges' => 0,
                'image' => $teacher->image,
            ]);
            $subject = Subject::factory()->create([
                'name' => fake()->colorName(),
                'lecturesCount' => 0,
                'subscriptions' => 0,
                'image' => 'Subjects/default.png',
            ]);
            $randomDigits = mt_rand(900000000, 999999999);
            Admin::factory()->create([
                'name' => fake()->name(),
                'userName' => fake()->name(),
                'password' => Hash::make('password'),
                'privileges' => rand(1, 2),
                'teacher_id' => null,
                'countryCode' => '+963',
                'image' => 'Admins/adminDefault.png',
                'number' => $randomDigits,
            ]);
            $lecture = Lecture::factory()->create([
                'name' => fake()->name(),
                // 'type' => ('PDF'),
                'file_360' => 'Files/360/default_360.mp4',
                'file_720' => 'Files/720/default_720.mp4',
                'file_1080' => 'Files/1080/default_1080.mp4',
                'description' => fake()->text(),
                'image' => 'Lectures/default.png',
                'subject_id' => rand(1, Subject::count()),
            ]);
            // $teacher->subjects()->attach($subject->id);
            $subject->subscriptions = Subject::withCount('users')->find($subject->id)->users_count;
            $subject->save();
        }
        foreach (Lecture::all() as $lecture) {
            $subject = Subject::findOrFail($lecture->subject_id);
            $subject->lectures()->attach($lecture->id);
            $subject->lecturesCount = $subject->lectures()->count();
            $subject->save();
        }

        $randomDigits = mt_rand(900000000, 999999999);
        Admin::factory()->create([
            'name' => 'admin',
            'userName' => 'admin',
            'password' => Hash::make('password'),
            'privileges' => 2,
            'teacher_id' => null,
            'countryCode' => '+963',
            'image' => 'Admins/adminDefault.png',
            'number' => $randomDigits,
        ]);
        $randomDigits = mt_rand(900000000, 999999999);
        Admin::factory()->create([
            'name' => 'semiadmin',
            'userName' => 'semiadmin',
            'password' => Hash::make('password'),
            'privileges' => 1,
            'teacher_id' => null,
            'countryCode' => '+963',
            'image' => 'Admins/adminDefault.png',
            'number' => $randomDigits,
        ]);
        $randomDigits = mt_rand(900000000, 999999999);
        $teacher = Teacher::factory()->create([
            'name' => 'teacher',
            'userName' => 'teacher',
            'countryCode' => '+963',
            'number' => $randomDigits,
            'image' => 'Admins/teacherDefault.png',
            'links' => '{"Facebook": "https://facebook", "Instagram": "https://instagram", "Telegram": "https://telegram", "YouTube":"https://youtube"}',
            'password' => Hash::make('password'),
        ]);
        Admin::factory()->create([
            'name' => $teacher->name,
            'userName' => $teacher->userName,
            'password' => $teacher->password,
            'privileges' => 0,
            'teacher_id' => $teacher->id,
            'countryCode' => '+963',
            'image' => $teacher->image,
            'number' => $teacher->number,
        ]);
        $this->call(SubjectSeeder::class);
        $this->call(TeacherSeeder::class);
        $this->call(LectureSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(UniversitySeeder::class);
    }
}
