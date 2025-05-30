<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model {
    protected $fillable = ['student_id','class_id','template_id','semester_id','issued_at'];
    public function student() { return $this->belongsTo(Student::class); }
    public function scores()  { return $this->hasMany(ReportScore::class); }
    public function template(){ return $this->belongsTo(ReportTemplate::class); }
}