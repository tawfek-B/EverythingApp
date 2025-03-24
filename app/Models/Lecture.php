<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;

class Lecture extends Model
{
    /** @use HasFactory<\Database\Factories\LectureFactory> */
    use HasFactory;
    protected $guarded = [];

    public function subject() {
        return $this->belongsTo(Subject::class);
    }
    function users() {
        return $this->belongsToMany(User::class, 'user_lecture');
    }
}
