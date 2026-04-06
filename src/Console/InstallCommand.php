<?php

namespace LaraOrVite\Framework\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'frontend:setup {name? : The name of the frontend directory}';
    protected $description = 'Create a separate Vite frontend with a custom name and setup Laravel API';

    public function handle()
    {
        $this->info('🚀 Starting LaraOrVite Setup...');

        $folderName = $this->argument('name') ?: 'frontend';

        if ($this->laravel->version() >= '11.0') {
            if (!File::exists(base_path('routes/api.php'))) {
                $this->info('📦 Installing Laravel API dependencies...');
                $this->call('install:api');
            }
        }

        $stubApiPath = __DIR__.'/../../stubs/api.php';

        if (File::exists($stubApiPath)) {
            File::copy($stubApiPath, base_path('routes/api.php'));
            $this->line(' ✅ API routes configured.');
        }

        $framework = $this->choice(
            'Which frontend framework do you want to use?',
            ['react', 'vue', 'svelte', 'vanilla', 'react-ts', 'vue-ts'],
            0
        );

        $frontendPath = resource_path($folderName);

        if (File::exists($frontendPath)) {
            if ($this->confirm("The 'resources/{$folderName}' directory already exists. Overwrite it?", true)) {
                File::deleteDirectory($frontendPath);
            } else {
                $this->error('❌ Setup aborted.');
                return;
            }
        }

        $this->info("🛠 Creating fresh Vite ($framework) project as '{$folderName}'...");

        $basePath = base_path();
        $resourcesPath = escapeshellarg($basePath . '/resources');
        $command = "cd {$resourcesPath} && npm create vite@latest {$folderName} -- --template {$framework} --yes";

        if (app()->environment() !== 'testing') {
            shell_exec($command);
        } else {
            File::makeDirectory($frontendPath, 0755, true, true);
        }

        $this->info('🎉 LaraOrVite Setup Successfully Completed!');
    }
}
