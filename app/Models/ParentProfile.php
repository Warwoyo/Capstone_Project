<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'name',
        'email',
        'phone_number',   // ← tambahkan
        'password',
        'role',           // ← tambahkan
    ];
    
    public function user() { return $this->belongsTo(User::class); }
}