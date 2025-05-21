<?php
// filepath: /home/anton/Documents/capstone/Capstone_Project/app/Models/Schedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['classroom_id', 'title', 'description'];
    
    /**
     * Get the classroom that owns the schedule.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
        public function subThemes()
    {
        return $this->hasMany(ScheduleDetail::class);
    }
    /**
     * Get the schedule details for the schedule.
     */
    public function details()
    {
        return $this->hasMany(ScheduleDetail::class);
    }
}