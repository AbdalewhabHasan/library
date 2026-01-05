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
    ->withMiddleware(function (Middleware $middleware) {

        // ▼▼▼ هذا هو السطر الجديد والمهم جداً ▼▼▼
        // استثناء مسار Stripe من حماية CSRF
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook', 
        ]);
        // ▲▲▲ انتهى السطر الجديد ▲▲▲

        // هذا هو الكود القديم الخاص بك، يبقى كما هو
        $middleware->alias([
            'publisher' => \App\Http\Middleware\Publisher::class,
            'listener' => \App\Http\Middleware\Listener::class,
            'is.admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    // ▼▼▼ هذا هو السطر الإضافي الذي يحل المشكلة ▼▼▼
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->create();
