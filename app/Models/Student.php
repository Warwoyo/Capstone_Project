<?php

// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = []; // agar mass-assignment berjalan
    public function registrationToken() { return $this->hasOne(RegistrationToken::class); }
}

