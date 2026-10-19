<?php

use App\Http\Middleware\CoordinatorMiddleware;
use App\Http\Middleware\ItcAdminMiddleware;
use App\Http\Middleware\LecturerMiddleware;
use App\Http\Middleware\ManagementMiddleware;
use App\Http\Middleware\StudentMiddleware;
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
            'admin' => CoordinatorMiddleware::class,
            'student' => StudentMiddleware::class,
            'coordinator' => CoordinatorMiddleware::class,
            'lecturer' => LecturerMiddleware::class,
            'itc' => ItcAdminMiddleware::class,
            'management' => ManagementMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
