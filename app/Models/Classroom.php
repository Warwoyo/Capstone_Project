<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id'];

    public function owner()     
    { 
        return $this->belongsTo(User::class,'owner_id'); 
    }
    
    public function students()
    {
        return $this->belongsToMany(Student::class, 'classroom_student');
    }
    

    public function attendances()
    { 
        return $this->hasMany(Attendance::class); 
    }
    
    // Keep the singular method for backward compatibility
    public function schedule() 
    { 
        return $this->hasMany(Schedule::class); 
    }
    
    // Add the plural method
    public function schedules()
    { 
        return $this->hasMany(Schedule::class); 
    }
    
    public function announcements()
    { 
        return $this->hasMany(Announcement::class); 
    }
    
    public function observations()
    {
        return $this->hasManyThrough(
            ObservationScore::class,
            ScheduleDetail::class,
            'classroom_id', // Foreign key on schedule_details table
            'schedule_detail_id', // Foreign key on observation_scores table
            'id', // Local key on classrooms table
            'id' // Local key on schedule_details table
        );
    }
    
    /**
     * Get the syllabuses for the classroom
     */
    public function syllabuses()
    {
        return $this->hasMany(Syllabus::class);
    }
}