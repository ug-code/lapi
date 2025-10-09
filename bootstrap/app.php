<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         // $middleware->append(JwtMiddleware::class);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('evds:check')->dailyAt('13:30');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
