<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Semester extends Model {
    protected $fillable = ['name','year','timeline','description'];
    public function templates() { return $this->hasMany(ReportTemplate::class); }
}
