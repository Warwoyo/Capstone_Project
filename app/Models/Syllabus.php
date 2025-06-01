<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;

    protected $table = 'syllabuses';

    protected $fillable = [
        'classroom_id',
        'title',
        'file_path',
        'file_name'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}