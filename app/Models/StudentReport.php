<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'student_id',
        'classroom_id',
        'issued_at',
        'notes'
    ];

    protected $casts = [
        'issued_at' => 'date',
    ];

    /**
     * Relasi ke template
     */
    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    /**
     * Relasi ke siswa
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relasi ke classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * Relasi ke nilai-nilai rapor
     */
    public function scores()
    {
        return $this->hasMany(ReportScore::class, 'report_id');
    }
}