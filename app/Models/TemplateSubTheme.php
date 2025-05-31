<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSubTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'code',
        'name',
        'order'
    ];

    /**
     * Relasi ke tema induk
     */
    public function theme()
    {
        return $this->belongsTo(TemplateTheme::class, 'theme_id');
    }

    /**
     * Relasi ke nilai/score siswa
     */
    public function scores()
    {
        return $this->hasMany(ReportScore::class, 'sub_theme_id');
    }

    /**
     * Scope berdasarkan kode
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}