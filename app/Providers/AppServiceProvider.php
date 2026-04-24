<?php

namespace App\Providers;

use App\Services\SiteSettingsService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helpersPath = app_path('helpers.php');
        if (is_file($helpersPath)) {
            require_once $helpersPath;
        }

        $this->app->singleton(SiteSettingsService::class, fn () => new SiteSettingsService);

        // If composer package discovery did not run (e.g. deploy with --no-scripts), DomPDF is never
        // registered and app('dompdf.wrapper') fails. Register only when the binding is still missing.
        if (class_exists(\Barryvdh\DomPDF\ServiceProvider::class) && ! $this->app->bound('dompdf.wrapper')) {
            $this->app->register(\Barryvdh\DomPDF\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
    }
}
