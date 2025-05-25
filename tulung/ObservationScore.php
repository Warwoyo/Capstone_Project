<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationScore extends Model
{
    protected $fillable = [
        'student_id',
        'schedule_detail_id',
        'score',
        'note'
    ];

    protected $casts = [
        'score' => 'integer'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scheduleDetail(): BelongsTo
    {
        return $this->belongsTo(ScheduleDetail::class);
    }
}