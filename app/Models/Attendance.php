<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_date','status','classroom_id','student_id'];

    protected $casts = ['attendance_date'=>'date'];

    public function classroom(){ return $this->belongsTo(Classroom::class); }
    public function student()  { return $this->belongsTo(Student::class); }
}
