<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureUrl();
        $this->configureVite();
    }

    private function configureCommands(): void
    {
        // Prevent destructive commands from running in production
        DB::prohibitDestructiveCommands(
            $this->app->isProduction()
        );
    }

    private function configureModels(): void
    {
        // Enable strict mode for models
        Model::shouldBeStrict();

        // Disable mass assignment protection
        Model::unguard();
    }

    private function configureUrl(): void
    {
        // Force HTTPS in production
        URL::forceHttps();
    }

    private function configureVite(): void
    {
        // Enable aggressive prefetching in production
        Vite::useAggressivePrefetching();
    }
}
