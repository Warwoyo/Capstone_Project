<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['classroom_id','student_id','semester','summary'];
    public function classroom(){ return $this->belongsTo(Classroom::class); }
    public function student()  { return $this->belongsTo(Student::class);   }
}

