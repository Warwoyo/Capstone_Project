<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    protected $fillable = ['classroom_id','title','description'];
    public function classroom(){ return $this->belongsTo(Classroom::class); }
}
