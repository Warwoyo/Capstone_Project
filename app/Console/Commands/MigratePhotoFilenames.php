<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;

class MigratePhotoFilenames extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:photo-filenames';

    /**
     * The console command description.
     */
    protected $description = 'Migrate old photo filenames to new millisecond timestamp format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting photo filename migration...');
        
        $students = Student::whereNotNull('photo')->get();
        $migratedCount = 0;
        
        foreach ($students as $student) {
            $oldPath = $student->photo;
            
            // Check if file exists
            if (!Storage::disk('public')->exists($oldPath)) {
                $this->warn("File not found: {$oldPath} for student: {$student->name}");
                continue;
            }
            
            // Check if already using new format (contains underscore and long number)
            $filename = basename($oldPath);
            if (preg_match('/^[A-Za-z0-9_]+_\d{13,}\.\w+$/', $filename)) {
                $this->line("Already migrated: {$filename}");
                continue;
            }
            
            // Get file extension
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            
            // Generate new filename
            $filenameData = Student::generatePhotoFilename($student->name, $extension);
            $newPath = 'students/' . $filenameData['filename'];
            
            // Copy file to new location
            if (Storage::disk('public')->copy($oldPath, $newPath)) {
                // Update database
                $student->update(['photo' => $newPath]);
                
                // Delete old file
                Storage::disk('public')->delete($oldPath);
                
                $migratedCount++;
                $this->info("Migrated: {$oldPath} → {$newPath}");
            } else {
                $this->error("Failed to migrate: {$oldPath}");
            }
        }
        
        $this->info("Migration completed. Migrated {$migratedCount} photo files.");
        
        return 0;
    }
}