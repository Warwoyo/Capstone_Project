<?php

// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Announcement,Schedule,Attendance,Observation,Report,Syllabus,Student};

class Classroom extends Model
{
    protected $fillable = ['name','description','owner_id'];

    /* alias biar Blade lama jalan */
    public function getTitleAttribute(): string { return $this->name; }

    /* ------- RELASI ------- */
    public function announcements() { return $this->hasMany(Announcement::class); }
    public function schedules()     { return $this->hasMany(Schedule::class); }
    public function attendances()   { return $this->hasMany(Attendance::class); }
    public function observations()  { return $this->hasMany(Observation::class); }
    public function reports()       { return $this->hasMany(Report::class); }          // rapor
    public function syllabuses()    { return $this->hasMany(Syllabus::class); }
    public function students()      { return $this->belongsToMany(Student::class); }
}

