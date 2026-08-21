<?php

use App\Http\Middleware\EnsureUserIsAdmin;
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
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        // O Mercado Pago chama esse endpoint diretamente (sem CSRF token,
        // como qualquer webhook de terceiro) — precisa ficar de fora da
        // verificação, senão toda notificação de pagamento é rejeitada.
        $middleware->validateCsrfTokens(except: [
            'webhooks/mercadopago',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
