<?php

namespace LaraOrVite\Framework\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class InstallCommand extends Command
{
    protected $signature = 'frontend:setup {name? : The name of the frontend directory}';
    protected $description = 'Create a separate Vite frontend with a custom name and setup Laravel API';

    public function handle()
    {
        $this->info('🚀 Starting LaraOrVite Setup...');

        $folderName = $this->argument('name') ?: 'frontend';
        $frontendPath = resource_path($folderName);

        // 1. API Setup (Laravel 11+ compatibility)
        if ($this->laravel->version() >= '11.0') {
            if (!File::exists(base_path('routes/api.php'))) {
                $this->info('📦 Installing Laravel API dependencies...');
                $this->call('install:api');
            }
        }

        // 2. Custom API Stub integration
        $stubApiPath = __DIR__.'/../../stubs/api.php';
        if (File::exists($stubApiPath)) {
            File::copy($stubApiPath, base_path('routes/api.php'));
            $this->line(' ✅ API routes configured.');
        }

        // 3. Choice of Frameworks (Including JS & TS)
        $framework = $this->choice(
            'Which frontend framework do you want to use?',
            [
                'vanilla', 'vanilla-ts',
                'vue', 'vue-ts',
                'react', 'react-ts',
                'preact', 'preact-ts',
                'lit', 'lit-ts',
                'svelte', 'svelte-ts'
            ],
            0
        );

        // 4. Directory Check & Cleanup
        if (File::exists($frontendPath)) {
            if ($this->confirm("The 'resources/{$folderName}' directory already exists. Overwrite it?", true)) {
                File::deleteDirectory($frontendPath);
            } else {
                $this->error('❌ Setup aborted.');
                return;
            }
        }

        $this->info("🛠 Creating fresh Vite ($framework) project as '{$folderName}'...");

        // 5. Run NPM Create Vite command using Process Facade
        if (app()->environment() !== 'testing') {
            // මෙහිදී Process::path() භාවිතා කරන්නේ resources folder එක ඇතුළත command එක run කිරීමටයි
            $process = Process::path(resource_path())
                ->timeout(300) // සමහර විට NPM package install වෙන්න වෙලාව යන නිසා විනාඩි 5ක් ලබා දී ඇත
                ->run("npm create vite@latest {$folderName} -- --template {$framework} --yes");

            if ($process->successful()) {
                $this->info(" ✅ Vite project '{$folderName}' created successfully.");
                $this->line("<info>Next steps:</info>");
                $this->line(" 1. cd resources/{$folderName}");
                $this->line(" 2. npm install");
                $this->line(" 3. npm run dev");
            } else {
                $this->error('❌ Vite creation failed!');
                $this->line($process->errorOutput());
                return;
            }
        } else {
            File::makeDirectory($frontendPath, 0755, true, true);
        }

        $this->info('🎉 LaraOrVite Setup Successfully Completed!');
    }
}
