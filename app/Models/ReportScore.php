<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportScore extends Model {
    protected $fillable = ['report_id','template_item_id','value','note'];
    public function item() { return $this->belongsTo(ReportTemplateItem::class,'template_item_id'); }
}