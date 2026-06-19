<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\RefreshUserPermissions;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\EnsureNotGuardian;
use \App\Http\Middleware\UpdateLastSeen;


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
            'not.guardian'       => EnsureNotGuardian::class,
        ]);
        $middleware->append(RefreshUserPermissions::class);
        $middleware->web(append: [
            CheckUserStatus::class,
            UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ================================================================
        // ✅ Helper — redirect لآخر صفحة بدون أي رسالة
        // ================================================================
        $handle403 = function ($request) {

            // ✅ AJAX — بدون رسالة
            if ($request->expectsJson()) {
                return response()->json([], 403);
            }

            $previous = url()->previous();
            $current  = url()->current();

            // ✅ يرجع لآخر صفحة لو مختلفة عن الحالية
            if ($previous && $previous !== $current) {
                return redirect()->to($previous);
            }

            // ✅ Fallback حسب الدور — بدون أي رسالة
            $user = auth()->user();

            $fallback = match (true) {
                !$user                            => route('login'),
                $user->hasRole('guardian')        => route('guardian.dashboard'),
                $user->hasRole('admin')           => route('dashboard'),
                $user->hasRole('general_manager') => route('dashboard'),
                $user->hasRole('manager')         => route('dashboard'),
                $user->hasRole('supervisor')      => route('dashboard'),
                $user->hasRole('teacher')         => route('dashboard'),
                default                           => route('login'),
            };

            // ✅ منع redirect loop
            if (url()->current() === $fallback) {
                return redirect()->route('login');
            }

            return redirect()->to($fallback);
        };

        // ================================================================
        // Spatie UnauthorizedException
        // ================================================================
        $exceptions->render(
            function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) use ($handle403) {
                return $handle403($request);
            }
        );

        // ================================================================
        // abort(403) من Controller أو Policy
        // ================================================================
        $exceptions->render(
            function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) use ($handle403) {
                return $handle403($request);
            }
        );

        // ================================================================
        // ✅ 419 — انتهاء صلاحية الجلسة
        // ================================================================
        $exceptions->render(
            function (\Illuminate\Session\TokenMismatchException $e, $request) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة',
                    ], 419);
                }

                return redirect()
                    ->back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'انتهت صلاحية الجلسة. يرجى إعادة المحاولة.');
            }
        );
    })->create();
