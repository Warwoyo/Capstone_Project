<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'semester_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke tema-tema dalam template ini
     */
    public function themes()
    {
        return $this->hasMany(TemplateTheme::class, 'template_id')->orderBy('order');
    }

    /**
     * Relasi ke assignment kelas
     */
    public function assignments()
    {
        return $this->hasMany(TemplateAssignment::class, 'template_id');
    }

    /**
     * Relasi ke kelas yang menggunakan template ini
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'template_assignments', 'template_id', 'classroom_id');
    }

    /**
     * Relasi ke laporan siswa yang menggunakan template ini
     */
    public function studentReports()
    {
        return $this->hasMany(StudentReport::class, 'template_id');
    }

    /**
     * Scope untuk template aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan semester
     */
    public function scopeBySemester($query, $semesterType)
    {
        return $query->where('semester_type', $semesterType);
    }
}