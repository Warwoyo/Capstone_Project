<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationToken extends Model
{
    // RegistrationToken.php
    protected $fillable = ['student_id','token','expires_at','used_at'];

    public static function generateFor(Student $student, int $days = 7): self
    {
        return self::create([
            'student_id' => $student->id,
            'token'      => strtoupper(Str::random(8)),
            'expires_at' => now()->addDays($days),
        ]);
    }

    
}
