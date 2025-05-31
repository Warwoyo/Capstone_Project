<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'classroom_id',
        'assigned_at',
        'is_current'
    ];

    protected $casts = [
        'assigned_at' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Relasi ke template
     */
    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    /**
     * Relasi ke classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * Scope untuk assignment yang sedang aktif
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}