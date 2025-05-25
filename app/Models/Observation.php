<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'schedule_detail_id',
        'student_id',
        'score',
        'observation_text',
        'observed_at',
        'observer_id'
    ];

    protected $casts = [
        'observed_at' => 'datetime'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function scheduleDetail()
    {
        return $this->belongsTo(ScheduleDetail::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function observer()
    {
        return $this->belongsTo(User::class, 'observer_id');
    }
}