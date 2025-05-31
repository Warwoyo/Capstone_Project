<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'sub_theme_id',
        'score',
        'notes'
    ];

    /**
     * Relasi ke laporan siswa
     */
    public function report()
    {
        return $this->belongsTo(StudentReport::class, 'report_id');
    }

    /**
     * Relasi ke sub-tema
     */
    public function subTheme()
    {
        return $this->belongsTo(TemplateSubTheme::class, 'sub_theme_id');
    }

    /**
     * Scope berdasarkan nilai tertentu
     */
    public function scopeByScore($query, $score)
    {
        return $query->where('score', $score);
    }
}