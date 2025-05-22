<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'classroom_id',
        'title', 
        'description',
        'created_at'
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scheduleDetails(): HasMany
    {
        return $this->hasMany(ScheduleDetail::class);
    }

    // Method to handle cascading deletes
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function($schedule) {
            $schedule->scheduleDetails()->delete();
        });
    }
}