<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationToken extends Model
{
    protected $guarded = [];
    public $timestamps = true;  // karena tabel punya created_at/updated_at

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
