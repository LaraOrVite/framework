<?php

namespace LaraOrVite\Framework\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class InstallCommand extends Command
{
    protected $signature = 'frontend:setup {name? : The name of the frontend directory}';
    protected $description = 'Create a standalone Vite frontend with auto-configured plugins and Laravel API';

    public function handle()
    {
        $this->info('🚀 Starting LaraOrVite Setup...');

        $folderName = $this->argument('name') ?: 'frontend';
        $frontendPath = resource_path($folderName);

        if ($this->laravel->version() >= '11.0') {
            if (!File::exists(base_path('routes/api.php'))) {
                $this->info('📦 Installing Laravel API dependencies...');
                $this->call('install:api');
            }
        }

        $stubApiPath = __DIR__.'/../../stubs/api.php';
        if (File::exists($stubApiPath)) {
            File::copy($stubApiPath, base_path('routes/api.php'));
            $this->line('');
            $this->line('API routes configured.');
        }

        $framework = $this->choice(
            'Which frontend framework?',
            ['react', 'react-ts', 'vue', 'vue-ts', 'react-native', 'svelte', 'svelte-ts', 'vanilla', 'vanilla-ts'],
            0
        );

        $isReactNative = ($framework === 'react-native');
        $isReact = str_contains($framework, 'react') && !$isReactNative;
        $isVue = str_contains($framework, 'vue');

        if ($isReactNative) {
            $addons = ['none'];
        } else {
            $options = ['None', 'Tailwind CSS', 'Axios', 'Lucide Icons', 'TanStack Query'];
            if ($isReact) $options = array_merge($options, ['Redux Toolkit', 'React Router']);
            if ($isVue) $options = array_merge($options, ['Pinia', 'Vue Router']);

            $addons = $this->choice('Select addons (comma-separated)', $options, 0, null, true);
        }

        if (File::exists($frontendPath)) {
            if (!$this->confirm("Overwrite 'resources/{$folderName}'?", true)) return;
            File::deleteDirectory($frontendPath);
        }

        if (app()->environment() === 'testing') {
            File::makeDirectory($frontendPath, 0755, true);
            return;
        }

        if ($isReactNative) {
            $this->info("📱 Creating Expo React Native project...");
            Process::path(resource_path())->timeout(600)->run("npx create-expo-app@latest {$folderName} --yes");

            $this->info("📦 Installing mobile dependencies...");
            Process::path($frontendPath)->run("npx expo install axios @react-navigation/native @react-navigation/native-stack react-native-screens react-native-safe-area-context");

            $this->info('🎉 React Native Setup Completed!');
            $this->line("\nRun: <comment>cd resources/{$folderName} && npx expo start</comment>");
            return;
        }

        $this->info("🛠 Creating Vite project...");
        Process::path(resource_path())->run("npm create vite@latest {$folderName} -- --template {$framework} --yes");

        $packages = ['axios'];
        if (in_array('Tailwind CSS', $addons)) $packages[] = '@tailwindcss/vite tailwindcss';
        if (in_array('Lucide Icons', $addons)) $packages[] = $isReact ? 'lucide-react' : 'lucide-vue-next';
        if (in_array('TanStack Query', $addons)) $packages[] = $isReact ? '@tanstack/react-query' : '@tanstack/vue-query';
        if (in_array('Redux Toolkit', $addons)) $packages[] = '@reduxjs/toolkit react-redux';
        if (in_array('React Router', $addons)) $packages[] = 'react-router-dom';
        if (in_array('Pinia', $addons)) $packages[] = 'pinia';
        if (in_array('Vue Router', $addons)) $packages[] = 'vue-router@4';

        $this->info("📦 Installing dependencies...");
        Process::path($frontendPath)->timeout(600)->run("npm install " . implode(' ', $packages));

        // 6. Generate Dynamic Vite Config
        $this->info("⚙️ Configuring Vite Plugins...");
        $this->generateViteConfig($frontendPath, $framework, $addons);

        $this->info('🎉 Setup Completed!');
        $this->line("\nRun: <comment>cd resources/{$folderName} && npm run dev</comment>");
    }

    protected function generateViteConfig($path, $framework, $addons)
    {
        $isReact = str_contains($framework, 'react');
        $isVue = str_contains($framework, 'vue');
        $isSvelte = str_contains($framework, 'svelte');
        $hasTailwind = in_array('Tailwind CSS', $addons);

        $imports = ["import { defineConfig } from 'vite'"];
        $plugins = [];

        // Framework Plugins
        if ($isReact) {
            $imports[] = "import react from '@vitejs/plugin-react'";
            $plugins[] = "react()";
        } elseif ($isVue) {
            $imports[] = "import vue from '@vitejs/plugin-vue'";
            $plugins[] = "vue()";
        } elseif ($isSvelte) {
            $imports[] = "import { svelte } from '@sveltejs/vite-plugin-svelte'";
            $plugins[] = "svelte()";
        }

        // Tailwind CSS v4 Plugin (Vite native)
        if ($hasTailwind) {
            $imports[] = "import tailwindcss from '@tailwindcss/vite'";
            $plugins[] = "tailwindcss()";
        }

        $importString = implode(";\n", $imports) . ";";
        $pluginString = implode(",\n    ", $plugins);

        $content = "{$importString}

                export default defineConfig({
                  plugins: [
                    {$pluginString}
                  ],
                })";

        $ext = File::exists("{$path}/vite.config.ts") ? 'ts' : 'js';
        File::put("{$path}/vite.config.{$ext}", $content);

        // Tailwind CSS File Setup
        if ($hasTailwind) {
            $cssPath = File::exists("{$path}/src/style.css") ? "{$path}/src/style.css" : "{$path}/src/index.css";
            if (File::exists($cssPath)) {
                File::put($cssPath, "@import \"tailwindcss\";\n" . File::get($cssPath));
            }
        }
    }
}
