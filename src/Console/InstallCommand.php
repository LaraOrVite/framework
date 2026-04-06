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

        // Laravel 11+ සඳහා API Setup කිරීම
        if ($this->laravel->version() >= '11.0') {
            if (!File::exists(base_path('routes/api.php'))) {
                $this->info('📦 Installing Laravel API dependencies...');
                $this->call('install:api');
            }
        }

        // Stubs වලින් api.php කොපි කිරීම
        $this->info('📝 Configuring custom API routes...');
        $stubApiPath = __DIR__.'/../../stubs/api.php';

        if (File::exists($stubApiPath)) {
            File::copy($stubApiPath, base_path('routes/api.php'));
            $this->line(' ✅ API routes configured.');
        } else {
            $this->warn(' ⚠️ Stub api.php not found.');
        }

        $framework = $this->choice(
            'Which frontend framework do you want to use?',
            ['react', 'vue', 'svelte', 'vanilla', 'react-ts', 'vue-ts'],
            0
        );

        $frontendPath = resource_path($folderName);

        // පවතින ෆෝල්ඩරයක් ඇත්නම් මකා දැමීම
        if (File::exists($frontendPath)) {
            if ($this->confirm("The 'resources/{$folderName}' directory already exists. Overwrite it?", true)) {
                File::deleteDirectory($frontendPath);
                $this->line(" 🗑 Old {$folderName} directory removed.");
            } else {
                $this->error('❌ Setup aborted.');
                return;
            }
        }

        $this->info("🛠 Creating fresh Vite ($framework) project as '{$folderName}'...");

        $basePath = base_path();
        // පාර (Path) ආරක්ෂිතව සකස් කිරීම (වරහන් තිබුණත් ප්‍රශ්නයක් නොවේ)
        $resourcesPath = escapeshellarg($basePath . '/resources');
        $command = "cd {$resourcesPath} && npm create vite@latest {$folderName} -- --template {$framework} -y";

        // පරීක්ෂණ (Testing) කරන විට npm දුවන්නේ නැතිව ෆෝල්ඩරය පමණක් සාදයි
        if (app()->environment() !== 'testing') {
            shell_exec($command);
        } else {
            File::makeDirectory($frontendPath, 0755, true, true);
        }

        $this->newLine();
        $this->info('🎉 LaraOrVite Setup Successfully Completed!');

        $this->table(
            ['Step', 'Command'],
            [
                ['1. Move to folder', "cd resources/{$folderName}"],
                ['2. Install packages', 'npm install'],
                ['3. Start Frontend', 'npm run dev'],
                ['4. Start Backend', 'php artisan serve'],
            ]
        );

        $this->comment("✨ Happy Coding! Your frontend is in: resources/{$folderName}");
    }
}