<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->renderable(function (RouteNotFoundException $e, Request $request) {
        //     // Interceptar RouteNotFoundException SOLO para API (para evitar error 500 por ruta login no encontrada)
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'No autorizado',
        //             'data' => null,
        //             'errors' => ['route' => [$e->getMessage()]]
        //         ], Response::HTTP_FORBIDDEN);
        //     }
        // });

        // 401 - No autenticado: Se requiere un token de acceso para acceder a estas rutas
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No autorizado',
                    'data' => null,
                    'errors' => null
                ], Response::HTTP_UNAUTHORIZED);
            }
        });

        // 403 - Acceso denegado: Se requiere un token de acceso con permisos para acceder a estas rutas
        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No tienes los permisos necesarios para realizar la acción',
                    'data' => null,
                    'errors' => null
                ], Response::HTTP_FORBIDDEN);
            }
        });

        // 404 - No encontrado
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Recurso no encontrado',
                    'data' => null,
                    'errors' => null
                ], Response::HTTP_NOT_FOUND);
            }
        });

        // 422 - Errores de validación de datos
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Errores de validación de datos',
                    'errors' => $e->errors(), // Detalles de los campos inválidos
                    'data' => null,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        // 500 - Error interno del servidor
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error interno del servidor',
                    'errors' => config('app.debug') ? [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'trace' => $e->getTrace()
                    ] : null,
                    'data' => null,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();
