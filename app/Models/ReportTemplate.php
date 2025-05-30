<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTemplate extends Model {
    protected $fillable = [
        'semester',      // 'Ganjil' / 'Genap'
        'tema_kode', 'tema',
        'sub_tema_kode', 'sub_tema',
        'description',
    ];
    public function semester() { return $this->belongsTo(Semester::class); }
    public function items() { return $this->hasMany(ReportTemplateItem::class)->orderBy('order'); }
    public function classes() { return $this->belongsToMany(Classroom::class,'class_report_template'); }
}