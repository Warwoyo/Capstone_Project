<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','nik','address'];

    public function user()      { return $this->belongsTo(User::class); }
    public function classrooms(){ return $this->hasMany(Classroom::class,'owner_id'); }
}

