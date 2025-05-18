<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'title',
        'description',
        'image',          // path file
        'published_at',   // tanggal tampil
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
