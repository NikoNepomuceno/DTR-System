<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/simple-auth.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'simple.auth' => \App\Http\Middleware\SimpleAuth::class,
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'employee.auth' => \App\Http\Middleware\EmployeeAuth::class,
            'login.rate.limit' => \App\Http\Middleware\LoginRateLimit::class,
            'secure.session' => \App\Http\Middleware\SecureSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
