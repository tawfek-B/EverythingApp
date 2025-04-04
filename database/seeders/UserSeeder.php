<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $randomDigits = mt_rand(90000000, 99999999);
            $user = User::factory()->create([
                'userName' => fake()->name(),
                'countryCode' => '+963',
                'number' => $randomDigits,
                'password' => Hash::make('password'),
                'isBanned' => 0,
            ]);
        }

        User::factory()->create([
            'userName' => "userName",
            'countryCode' => '+963',
            'number' => "999999999",
            'password' => Hash::make('password'),
        ]);
        //
    }
}
