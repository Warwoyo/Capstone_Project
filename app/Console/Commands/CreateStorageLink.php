<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateStorageLink extends Command
{
    protected $signature = 'storage:link-custom';
    protected $description = 'Create symbolic link from public/storage to storage/app/public';

    public function handle()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (File::exists($link)) {
            $this->info('The "public/storage" directory already exists.');
            return;
        }

        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
        }

        File::link($target, $link);

        $this->info('The [public/storage] directory has been linked.');
    }
}