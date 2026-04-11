<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
        //
    }
}
