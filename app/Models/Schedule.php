<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['classroom_id','title'];

    public function classroom()     { return $this->belongsTo(Classroom::class); }
    public function details()       { return $this->hasMany(ScheduleDetail::class); }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
