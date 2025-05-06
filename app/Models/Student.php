<?php

// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = []; // agar mass-assignment berjalan
    public function parent() { return $this->belongsTo(User::class, 'parent_id'); }
    public function parents()     { return $this->hasMany(ParentModel::class); }
    public function mother()      { return $this->hasOne(ParentModel::class)->where('relation','mother'); }
    public function father()      { return $this->hasOne(ParentModel::class)->where('relation','father'); }    
    public function registrationToken() { return $this->hasOne(RegistrationToken::class); }
}

