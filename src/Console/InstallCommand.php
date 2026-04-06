<?php

namespace LaraOrVite\Framework\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class InstallCommand extends Command
{
    protected $signature = 'frontend:setup {name? : The name of the frontend directory}';
    protected $description = 'Create a separate Vite frontend, setup Laravel API and configure Vite dependencies';

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
            ['lit', 'lit-ts', 'preact', 'preact-ts', 'react', 'react-ts', 'svelte', 'svelte-ts', 'vanilla', 'vanilla-ts', 'vue', 'vue-ts'],
            4
        );

        // 4. Addon Selection
        $isReact = str_contains($framework, 'react');
        $isVue = str_contains($framework, 'vue');

        $options = ['None', 'Tailwind CSS', 'Axios', 'Lucide Icons', 'TanStack Query'];
        if ($isReact) {
            $options = array_merge($options, ['Redux Toolkit (with React-Redux)', 'Zustand', 'React Router']);
        } elseif ($isVue) {
            $options = array_merge($options, ['Pinia', 'Vue Router']);
        }

        $addons = $this->choice(
            'Select additional packages to install (comma-separated numbers)',
            $options,
            0,
            null,
            true
        );

        // 5. Directory Check & Cleanup
        if (File::exists($frontendPath)) {
            if ($this->confirm("The 'resources/{$folderName}' directory already exists. Overwrite it?", true)) {
                File::deleteDirectory($frontendPath);
            } else {
                $this->error('❌ Setup aborted.');
                return;
            }
        }

        // 6. Execution
        if (app()->environment() !== 'testing') {
            $this->info("🛠 Creating fresh Vite ($framework) project...");

            // Step A: Create Vite Project
            $createVite = Process::path(resource_path())
                ->timeout(300)
                ->run("npm create vite@latest {$folderName} -- --template {$framework} --yes");

            if (!$createVite->successful()) {
                $this->error('❌ Vite creation failed!');
                $this->line($createVite->errorOutput());
                return;
            }

            // Step B: NPM Install Core & Laravel Vite Plugin
            $this->info("📦 Installing base dependencies & Laravel Vite Plugin...");

            Process::path($frontendPath)->timeout(600)->run("npm install laravel-vite-plugin --save-dev");
            Process::path($frontendPath)->timeout(600)->run("npm install");

            // Step C: Install Addons
            if (!in_array('None', $addons)) {
                $packages = [];
                if (in_array('Tailwind CSS', $addons)) $packages[] = 'tailwindcss postcss autoprefixer';
                if (in_array('Axios', $addons)) $packages[] = 'axios';
                if (in_array('Lucide Icons', $addons)) $packages[] = $isReact ? 'lucide-react' : ($isVue ? 'lucide-vue-next' : 'lucide');
                if (in_array('TanStack Query', $addons)) $packages[] = $isReact ? '@tanstack/react-query' : '@tanstack/vue-query';
                if (in_array('Redux Toolkit (with React-Redux)', $addons)) $packages[] = '@reduxjs/toolkit react-redux';
                if (in_array('Zustand', $addons)) $packages[] = 'zustand';
                if (in_array('Pinia', $addons)) $packages[] = 'pinia';
                if (in_array('React Router', $addons)) $packages[] = 'react-router-dom';
                if (in_array('Vue Router', $addons)) $packages[] = 'vue-router@4';

                if (!empty($packages)) {
                    $this->info("➕ Installing selected addons...");
                    $pkgString = implode(' ', $packages);
                    Process::path($frontendPath)->timeout(600)->run("npm install $pkgString");

                    if (in_array('Tailwind CSS', $addons)) {
                        Process::path($frontendPath)->run("npx tailwindcss init -p");
                    }
                }
            }

            // Step D: Configure Vite for Laravel (Optional: Update vite.config.js)
            $this->info("⚙️ Configuring vite.config.js for Laravel...");
            $this->updateViteConfig($frontendPath, $isReact, $isVue);

        } else {
            File::makeDirectory($frontendPath, 0755, true, true);
        }

        $this->info('🎉 LaraOrVite Setup Successfully Completed!');
        $this->line("\n<info>Next steps:</info>");
        $this->line(" 1. <comment>cd resources/{$folderName}</comment>");
        $this->line(" 2. <comment>npm run dev</comment>");
    }

    /**
     * Update vite.config.js to include Laravel integration.
     */
    protected function updateViteConfig($path, $isReact, $isVue)
    {
        $viteConfigPath = "{$path}/vite.config.js";
        if (File::exists("{$path}/vite.config.ts")) $viteConfigPath = "{$path}/vite.config.ts";

        $plugin = $isReact ? "react()" : ($isVue ? "vue()" : "");
        $import = $isReact ? "import react from '@vitejs/plugin-react';" : ($isVue ? "import vue from '@vitejs/plugin-vue';" : "");

        $configContent = <<<EOD
        import { defineConfig } from 'vite';
        import laravel from 'laravel-vite-plugin';
        {$import}

        export default defineConfig({
            plugins: [
                laravel({
                    input: ['src/main.jsx', 'src/style.css'], // Adjust based on framework
                    refresh: true,
                }),
                {$plugin}
            ],
        });
        EOD;

        File::put($viteConfigPath, $configContent);
    }
}
