<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $guarded = [];

    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }

    function users() {
        return $this->belongsToMany(User::class, 'subscriptions');
    }
    function lectures() {
        return $this->belongsToMany(Lecture::class, 'subject_lecture');
    }
}
