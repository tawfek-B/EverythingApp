<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lecture;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class LectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 10; $i++) {
            // $lectureTypes = ['MP4', 'PDF'];
            $randSub = rand(1,Subject::count());

            $lecture = Lecture::factory()->create([
                'name' => fake()->name(),
                'description' => fake()->text(),
                // 'type' => $lectureTypes[array_rand($lectureTypes)],
                'file_360' => 'Files/360/default_360.mp4',
                'file_720' => 'Files/720/default_720.mp4',
                'file_1080' => 'Files/1080/default_1080.mp4',
                'subject_id' => $randSub,
                'image' => 'Images/Lectures/default.png',
            ]);
            $subject = Subject::findOrFail($randSub);
            Subject::findOrFail($randSub)->lectures()->attach($lecture->id);

            $subject->lecturesCount = Subject::withCount('lectures')->find($subject->id)->lectures_count;
            $subject->save();
        }
        //
    }
}
