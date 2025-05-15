<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    use HasFactory;


    protected $fillable = [
        'student_id', 'name', 'relation',
        'phone', 'email', 'nik',
        'occupation', 'address', 'user_id',
    ];
    
    
    public function student() { return $this->belongsTo(Student::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function registrationTokens()
        {
        return $this->hasMany(RegistrationToken::class);
    }
}
