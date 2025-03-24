<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0;$i<10;$i++) {
            Subscription::factory()->create([
                'user_id'=> rand(1, User::count()),
                $subjectID = 'subject_id'=> rand(1, Subject::count()),
            ]);

        }

        foreach (Subject::all() as $subject) {
            $subject->subscriptions = Subject::withCount('users')->find($subject->id)->users_count;
            $subject->save();
        }
        //
    }
}
