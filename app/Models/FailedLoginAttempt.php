<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'ip_address',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    public static function getRecentAttempts($identifier, $minutes = 30)
    {
        return self::where('identifier', $identifier)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    public static function isBlocked($identifier, $maxAttempts = 5, $minutes = 30)
    {
        return self::getRecentAttempts($identifier, $minutes) >= $maxAttempts;
    }

    public static function clearAttempts($identifier)
    {
        return self::where('identifier', $identifier)->delete();
    }

    public static function clearAllAttempts(array $identifiers)
    {
        return self::whereIn('identifier', $identifiers)->delete();
    }
}