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

        // 3. Framework Selection
        $framework = $this->choice(
            'Which frontend framework do you want to use?',
            [
                'lit', 'lit-ts',
                'preact', 'preact-ts',
                'react', 'react-ts',
                'svelte', 'svelte-ts',
                'vanilla', 'vanilla-ts',
                'vue', 'vue-ts'
            ],
            4 // Default to 'react'
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

        // 5. Execution (Skip heavy tasks during testing)
        if (app()->environment() !== 'testing') {

            $this->info("🛠 Creating fresh Vite ($framework) project as '{$folderName}'...");

            // Step A: Create Vite Project
            $createVite = Process::path(resource_path())
                ->timeout(300)
                ->run("npm create vite@latest {$folderName} -- --template {$framework} --yes");

            if (!$createVite->successful()) {
                $this->error('❌ Vite creation failed!');
                $this->line($createVite->errorOutput());
                return;
            }

            $this->info(" ✅ Vite project '{$folderName}' created.");

            // Step B: NPM Install
            $this->info("📦 Installing NPM dependencies... (This may take a minute)");

            $npmInstall = Process::path($frontendPath)
                ->timeout(600) // 10 minutes for slow connections
                ->run("npm install");

            if ($npmInstall->successful()) {
                $this->info(" ✅ NPM dependencies installed successfully.");
            } else {
                $this->warn(" ⚠️ Vite project created, but 'npm install' failed. You may need to run it manually.");
                $this->line($npmInstall->errorOutput());
            }

        } else {
            // Testing වලදී folder එකක් පමණක් සාදා skip කරයි
            File::makeDirectory($frontendPath, 0755, true, true);
        }

        $this->info('🎉 LaraOrVite Setup Successfully Completed!');
        $this->line("");
        $this->line("<info>To start development:</info>");
        $this->line(" 1. <comment>cd resources/{$folderName}</comment>");
        $this->line(" 2. <comment>npm run dev</comment>");
    }
}
