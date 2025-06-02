<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class RegistrationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isUsed()
    {
        return !is_null($this->used_at);
    }

    public function markAsUsed()
    {
        $this->used_at = now();
        $this->save();
    }

    public static function generateFor(Student $student, int $days = 7): self
    {
        return self::create([
            'student_id' => $student->id,
            'token'      => strtoupper(Str::random(8)),
            'expires_at' => now()->addDays($days),
        ]);
    }

    
}
