<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTemplateItem extends Model {
    protected $fillable = ['template_id','kode','label','parent_id','order'];
    public function template() { return $this->belongsTo(ReportTemplate::class); }
    public function children() { return $this->hasMany(self::class,'parent_id'); }
    public function parent()   { return $this->belongsTo(self::class,'parent_id'); }
}