<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;

class CleanupUnusedPhotos extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cleanup:photos';

    /**
     * The console command description.
     */
    protected $description = 'Clean up unused student photos from storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting photo cleanup...');
        
        // Get all photo files in students directory
        $allFiles = Storage::disk('public')->files('students');
        
        // Get all photos referenced in database
        $usedPhotos = Student::whereNotNull('photo')->pluck('photo')->toArray();
        
        $deletedCount = 0;
        
        foreach ($allFiles as $file) {
            if (!in_array($file, $usedPhotos)) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
                $this->line("Deleted: {$file}");
            }
        }
        
        $this->info("Cleanup completed. Deleted {$deletedCount} unused photos.");
        
        return 0;
    }
}