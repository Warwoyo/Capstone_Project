<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id'];   // <── tambahkan owner_id


    public function owner()     { return $this->belongsTo(User::class,'owner_id'); }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'classroom_student');
    }


    public function attendances(){ return $this->hasMany(Attendance::class); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function announcements(){ return $this->hasMany(Announcement::class); }
    public function observations(){ return $this->hasManyThrough(Observation::class, ScheduleDetail::class); }
}

