<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTheme extends Model
{
   protected $fillable = ['title', 'start_date', 'end_date', 'week', 'schedule_id'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

}