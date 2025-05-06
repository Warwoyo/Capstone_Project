<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Announcement.php
class Announcement extends Model
{
    protected $fillable = ['classroom_id','title','image','published_at','visible_until','description'];

    public function classroom() { return $this->belongsTo(Classroom::class); }
}

