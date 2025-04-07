<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $randomDigits = mt_rand(100000000, 999999999);
            $teacher = Teacher::factory()->create([
                'userName' => fake()->name(),
                'name' => fake()->name(),
                'countryCode' => '+963',
                'number' => $randomDigits,
                'password' => Hash::make('password'),
                'image' => 'Images/Admins/teacherDefault.png',
                'links' => '{"Facebook": "https://facebook", "Instagram": "https://instagram", "Telegram": "https://telegram", "YouTube": "https://youtube"}',
            ]);
            Subject::where('id', rand(1, Subject::count()))->first()->teachers()->attach($teacher->id);

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
        }
        //
    }
}
