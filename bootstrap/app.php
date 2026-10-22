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
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust only local reverse proxies (nginx, Laragon, shared hosting load balancers)
        // Change '127.0.0.1,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16' to your specific
        // production proxy IP for maximum security.
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
            'api/*',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\EnsureTwoFactorSetup::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
