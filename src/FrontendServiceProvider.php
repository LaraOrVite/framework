<?php

namespace LaraOrVite\Framework;

use Illuminate\Support\ServiceProvider;
use LaraOrVite\Framework\Console\InstallCommand;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs/laraorvite'),
            ], 'laraorvite-stubs');
        }
    }
}