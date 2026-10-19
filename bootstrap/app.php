<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\CoordinatorMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'coordinator' => \App\Http\Middleware\CoordinatorMiddleware::class,
            'lecturer' => \App\Http\Middleware\LecturerMiddleware::class,
            'itc' => \App\Http\Middleware\ItcAdminMiddleware::class,
            'management' => \App\Http\Middleware\ManagementMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();