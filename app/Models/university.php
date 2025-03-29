<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class university extends Model
{
    /** @use HasFactory<\Database\Factories\UniversityFactory> */
    use HasFactory;

    protected $guarded = [];

    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_university');
    }
}
