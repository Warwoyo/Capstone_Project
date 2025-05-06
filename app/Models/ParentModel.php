<?php

// app/Models/Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ParentModel extends Model {
    protected $table = 'parents';
    protected $fillable = ['student_id','relation','name','nik','phone','email','address','occupation'];
    public function student() { return $this->belongsTo(Student::class); }
}