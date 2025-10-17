<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Illuminate\Log\log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        // commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        })->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ApiException) {
                    return response()->json(['errors' => [
                        'code' => $e->getErrorCode()->name,
                        'message' => $e->getMessage(),
                        'details' => $e->getDetails()
                    ]], $e->getCode());
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json(['errors' => [
                        'code' => 'ErrUnauthorized',
                        'message' => 'Unauthenticated request'
                    ]], 401);
                }

                if ($e instanceof AccessDeniedHttpException || $e instanceof AuthorizationException) {
                    return response()->json(['errors' => [
                        'code' => 'ErrForbidden',
                        'message' => 'This action is unauthorized'
                    ]], 403);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json(['errors' => [
                        'code' => 'ErrNotFound',
                        'message' => 'Resource not found'
                    ]], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json(['errors' => [
                        'code' => 'ErrMethodNotAllowed',
                        'message' => 'Request method not allowed'
                    ]], 405);
                }

                log($e);

                return response()->json(['errors' => ['code' => 'ErrUnknown', 'message' => 'Unknown error occurred']], 500);
            }

            return parent::render($request, $e);
        });
    })->create();
