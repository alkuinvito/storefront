<?php

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
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
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
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
                        'message' => 'Unauthorized request'
                    ]], 401);
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

                try {
                    $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                } catch (Throwable) {
                    $statusCode = 500;
                }

                log($e);

                return response()->json(['errors' => ['code' => ApiErrorCode::ErrUnknown->name, 'message' => ApiErrorCode::ErrUnknown->value]], $statusCode);
            }

            return parent::render($request, $e);
        });
    })->create();
