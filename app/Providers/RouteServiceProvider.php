<?php

    namespace App\Providers;


    class RouteServiceProvider extends ServiceProvider
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
             Route::middleware('user.type', UserTypeMiddleware::class);
             parent::boot(); 
        }
    }