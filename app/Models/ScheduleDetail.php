<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleDetail extends Model
{
    protected $fillable = [
        'schedule_id',
        'classroom_id',
        'title',
        'start_date',
        'end_date',
        'week'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'week' => 'integer'
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
    public function scheduleDetails()
    {
        return $this->hasMany(SubTheme::class); // or whatever your SubTheme model is called
    }
}