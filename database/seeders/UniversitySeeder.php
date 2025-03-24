<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\university;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        University::create([
            'name' => 'Damascus University',
            'image' => 'Universities/universityDefault.png',
        ]);
        University::create([
            'name' => 'Homs University',
            'image' => 'Universities/universityDefault.png',
        ]);
        University::create([
            'name' => 'Tishreen University',
            'image' => 'Universities/universityDefault.png',
        ]);
        University::create([
            'name' => 'Aleppo University',
            'image' => 'Universities/universityDefault.png',
        ]);
        //
    }
}
