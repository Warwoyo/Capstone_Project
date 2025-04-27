<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /* -------------------------------------------------
     |  Mass assignment
     * ------------------------------------------------*/
    protected $guarded = [];   // sudah terbuka semua

    /* -------------------------------------------------
     |  Hidden & Cast
     * ------------------------------------------------*/
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /* -------------------------------------------------
     |  Relasi profil
     * ------------------------------------------------*/
    public function teacherProfile() { return $this->hasOne(TeacherProfile::class); }
    public function parentProfile()  { return $this->hasOne(ParentProfile::class); }

    /* -------------------------------------------------
     |  Relasi siswa ↔ orang-tua (pivot student_parent)
     * ------------------------------------------------*/
    public function students()
    {
        return $this->belongsToMany(
            Student::class,      // model target
            'student_parent',    // nama tabel pivot
            'user_id',           // FK pivot → users
            'student_id'         // FK pivot → students
        );
    }
}
