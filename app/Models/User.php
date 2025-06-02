<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

// app/Models/User.php
class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name','email','role','password','temp_password'
    ];

    /* === RELASI === */
    public function contacts()   { return $this->hasMany(UserContact::class); }
    public function primaryContact()
    {
        return $this->hasOne(UserContact::class)->where('is_primary', true);
    }
    public function parentProfile() { return $this->hasOne(ParentProfile::class); }
    public function students()      { return $this->belongsToMany(Student::class); }
}
