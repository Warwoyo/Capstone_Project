<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'name',
        'birth_date',
        'gender',
        'photo',
        'medical_history',
    ];
    /* ─────────── Relasi ke parent profiles ─────────── */

    // Semua orang-tua / wali
    public function parents(): HasMany
    {
        return $this->hasMany(ParentProfile::class);
    }

    // Ayah saja
    public function father(): HasOne
    {
        return $this->hasOne(ParentProfile::class)
                    ->where('relation', 'father');
    }

    // Ibu saja
    public function mother(): HasOne
    {
        return $this->hasOne(ParentProfile::class)
                    ->where('relation', 'mother');
    }

    // (Opsional) wali
    public function guardian(): HasOne
    {
        return $this->hasOne(ParentProfile::class)
                    ->where('relation', 'guardian');
    }
    // app/Models/Student.php
    public function registrationTokens()
    {
        return $this->hasMany(RegistrationToken::class);
    }

        public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /* ─────────── Relasi lain (kelas, dsb) tambahkan di bawah sini ─────────── */
}
