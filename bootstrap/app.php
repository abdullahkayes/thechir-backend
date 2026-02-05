<?php

ini_set('memory_limit', '512M');

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->alias([
            'coustomer.auth' => \App\Http\Middleware\CoustomerAuth::class,
            'reseller.auth' => \App\Http\Middleware\ResellerAuth::class,
            'b2b.auth' => \App\Http\Middleware\B2bAuth::class,
            'distributer.auth' => \App\Http\Middleware\DistributerAuth::class,
            'amazon.auth' => \App\Http\Middleware\AmazonAuth::class,
            'cacheResponse' => \Spatie\ResponseCache\Middlewares\CacheResponse::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
        ]);

        // Apply CORS middleware to all web routes
        $middleware->web(prepend: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);

        // Apply CORS middleware to all API routes
        $middleware->api(prepend: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
