<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDetail extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id','sub_title','start_date','end_date','week'];

    protected $casts = ['start_date'=>'date','end_date'=>'date'];

    public function schedule()     { return $this->belongsTo(Schedule::class); }
    public function observations() { return $this->hasMany(Observation::class); }
}

