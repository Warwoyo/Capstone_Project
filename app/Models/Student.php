<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'name',
        'birth_date',
        'gender',
        'photo',
        'medical_history',
    ];
    /* ─────────── Relasi ke parent profiles ─────────── */

    // Semua orang-tua / wali
    public function parents(): HasMany
    {
        return $this->hasMany(ParentProfile::class);
    }

    // Ayah saja
    public function father(): HasOne
    {
        return $this->hasOne(ParentProfile::class)
                    ->where('relation', 'father');
    }

    // Ibu saja
    public function mother(): HasOne
    {
        return $this->hasOne(ParentProfile::class)
                    ->where('relation', 'mother');
    }

    // (Opsional) wali
    public function guardian(): HasOne
    {
        return $this->hasOne(ParentProfile::class)
                    ->where('relation', 'guardian');
    }
    // app/Models/Student.php
    public function registrationTokens()
    {
        return $this->hasMany(RegistrationToken::class);
    }

        public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function classroom()
    {
        return $this->classrooms()->first();
    }
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student');
    }

    /**
     * Relasi dengan ParentProfile
     */
    public function parentProfiles()
    {
        return $this->hasMany(ParentProfile::class);
    }

    /**
     * Relasi ke orang tua (parent user)
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Relasi ke laporan siswa
     */
    public function reports()
    {
        return $this->hasMany(StudentReport::class, 'student_id');
    }

    /**
     * Get photo URL accessor
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    /**
     * Handle photo upload with custom naming using millisecond timestamp
     */
    public static function handlePhotoUpload($file, $studentName, $oldPhotoPath = null)
    {
        if (!$file) return $oldPhotoPath;
        
        // Delete old photo if exists
        if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
            Storage::disk('public')->delete($oldPhotoPath);
        }
        
        $extension = $file->getClientOriginalExtension();
        
        // Generate filename using millisecond timestamp
        $filenameData = self::generatePhotoFilename($studentName, $extension);
        $filename = $filenameData['filename'];
        
        // Log filename generation info for debugging
        \Log::info('Photo filename generated', [
            'original_name' => $studentName,
            'safe_name' => $filenameData['safe_name'],
            'timestamp' => $filenameData['timestamp'],
            'format' => $filenameData['format'],
            'final_filename' => $filename
        ]);
        
        return $file->storeAs('students', $filename, 'public');
    }

    /**
     * Generate timestamp-based filename for photos
     */
    public static function generatePhotoFilename($studentName, $extension)
    {
        $safeName = preg_replace('/[^A-Za-z0-9\-]/', '_', $studentName);
        
        // Use millisecond timestamp for unique filenames
        if (method_exists(now(), 'getTimestampMs')) {
            $timestamp = now()->getTimestampMs();
            $format = 'milliseconds';
        } else {
            $timestamp = (int)(microtime(true) * 1000);
            $format = 'microtime_ms';
        }
        
        return [
            'filename' => "{$safeName}_{$timestamp}.{$extension}",
            'timestamp' => $timestamp,
            'format' => $format,
            'safe_name' => $safeName
        ];
    }

    /**
     * Check if photo file exists in storage
     */
    public function photoExists()
    {
        return $this->photo && Storage::disk('public')->exists($this->photo);
    }

    /**
     * Get full path to photo
     */
    public function getPhotoPath()
    {
        return $this->photo ? storage_path('app/public/' . $this->photo) : null;
    }

    /**
     * Debug photo information
     */
    public function getPhotoDebugInfo()
    {
        if (!$this->photo) {
            return ['status' => 'no_photo'];
        }

        return [
            'photo_field' => $this->photo,
            'file_exists' => $this->photoExists(),
            'full_path' => $this->getPhotoPath(),
            'url' => $this->photo_url,
            'public_path' => public_path('storage/' . $this->photo)
        ];
    }

    /* ─────────── Relasi lain (kelas, dsb) tambahkan di bawah sini ─────────── */
}
