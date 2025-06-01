<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SetupStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'setup:storage';

    /**
     * The console command description.
     */
    protected $description = 'Setup storage directories and symbolic links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up storage...');
        
        // Create students directory if it doesn't exist
        if (!Storage::disk('public')->exists('students')) {
            Storage::disk('public')->makeDirectory('students');
            $this->info('Created students directory in storage/app/public/');
        }
        
        // Check if storage link exists
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');
        
        if (!File::exists($linkPath)) {
            $this->call('storage:link');
            $this->info('Created storage symbolic link');
        } else {
            $this->info('Storage symbolic link already exists');
        }
        
        // Set proper permissions
        if (File::exists($targetPath)) {
            chmod($targetPath, 0755);
            $this->info('Set proper permissions for storage directory');
        }
        
        $this->info('Storage setup completed!');
        
        return 0;
    }
}