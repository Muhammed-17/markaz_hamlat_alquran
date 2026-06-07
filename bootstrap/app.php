<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\RefreshUserPermissions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
        $middleware->append(RefreshUserPermissions::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $handle403 = function ($request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'الصفحة غير موجودة'], 404);
            }

            $previous = url()->previous();
            $current  = url()->current();

            if ($previous && $previous !== $current) {
                return redirect()->to($previous);
            }

            $user = auth()->user();

            $fallback = match (true) {
                $user?->hasRole('guardian')   => route('guardian.dashboard'),
                $user?->hasRole('admin')      => route('dashboard'),
                $user?->hasRole('supervisor') => route('dashboard'),
                $user?->hasRole('teacher')    => route('dashboard'),
                default                       => route('login'),
            };

            return redirect()->to($fallback);
        };

        $exceptions->render(
            function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) use ($handle403) {
                return $handle403($request);
            }
        );

        $exceptions->render(
            function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) use ($handle403) {
                return $handle403($request);
            }
        );

        // ✅ 419 — انتهاء صلاحية الجلسة
        $exceptions->render(
            function (\Illuminate\Session\TokenMismatchException $e, $request) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة'], 419);
                }

                return redirect()
                    ->back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'انتهت صلاحية الجلسة. يرجى إعادة المحاولة.');
            }
        );
    })->create();
