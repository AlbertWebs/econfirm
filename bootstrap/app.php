<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\RecordSiteTraffic::class);
        $middleware->alias([
            'user-access' => \App\Http\Middleware\UserAccess::class,
            'api.auth' => \App\Http\Middleware\AuthenticateApi::class,
            'admin.verified' => \App\Http\Middleware\EnsureAdminEmailIsVerified::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            $adminGuestOk = $request->is('admin/login')
                || $request->is('admin/login/two-factor')
                || $request->is('admin/email/verify/*')
                || $request->is('admin/email/resend-verification');

            if (($request->is('admin') || $request->is('admin/*')) && ! $adminGuestOk) {
                return route('admin.login');
            }

            if (($request->is('developer') || $request->is('developer/*')) && ! $request->is('developer/login')) {
                return Route::has('developer.login') ? route('developer.login') : url('/developer/login');
            }

            return Route::has('login') ? route('login') : url('/login');
        });
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.dashboard');
            }

            return route('user.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
