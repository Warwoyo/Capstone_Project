<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    use HasFactory;

    protected $table = 'student_reports';

    protected $fillable = [
        'classroom_id',
        'student_id', 
        'template_id',
        'scores',
        'teacher_comment',
        'parent_comment',
        'physical_data',
        'attendance_data',
        'theme_comments',
        'sub_theme_comments'
    ];

    protected $casts = [
        'scores' => 'array',
        'physical_data' => 'array',
        'attendance_data' => 'array',
        'theme_comments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the classroom that owns the report
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get the student that owns the report
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the template used for this report
     */
    public function template()
    {
        return $this->belongsTo(\App\Models\ReportTemplate::class, 'template_id');
    }

    /**
     * Scope to filter by classroom
     */
    public function scopeForClassroom($query, $classroomId)
    {
        return $query->where('classroom_id', $classroomId);
    }

    /**
     * Scope to filter by student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter by template
     */
    public function scopeForTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }
}