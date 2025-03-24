<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory;

    protected $guarded = [];

    function subjects() {
        return $this->belongsToMany(Subject::class,'teacher_subject');
    }
    public function universities() {
        return $this->belongsToMany(university::class, 'teacher_university');
    }
}
