<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['classroom_id','student_id','attendance_date','status'];
    public function classroom() { return $this->belongsTo(Classroom::class); }
    public function student()   { return $this->belongsTo(Student::class);   }
}
