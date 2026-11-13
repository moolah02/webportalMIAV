<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\ForcePasswordChange::class);

        // Lets a route demand a specific token ability. The reporting feed uses
        // it so a BI token can read the feed and nothing else, even though it
        // authenticates through the same guard as a technician's tablet.
        $middleware->alias([
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability'   => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);

        // The alias above only binds where a route asks for it, and the tablet
        // API asks only for auth:sanctum - so this runs everywhere and keeps a
        // reporting token off every endpoint except the feed.
        $middleware->appendToGroup('api', \App\Http\Middleware\RestrictReportingTokens::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Ensure API consumers (mobile apps) consistently receive JSON for auth failures
        // even if they forget to send `Accept: application/json`.
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return null;
        });
    })->create();
