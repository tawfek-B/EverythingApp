<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;
use App\Models\User;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;


    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
