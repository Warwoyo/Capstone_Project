<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'code',
        'name',
        'order'
    ];

    /**
     * Relasi ke template induk
     */
    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    /**
     * Relasi ke sub-tema
     */
    public function subThemes()
    {
        return $this->hasMany(TemplateSubTheme::class, 'theme_id')->orderBy('order');
    }

    /**
     * Scope berdasarkan kode
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}