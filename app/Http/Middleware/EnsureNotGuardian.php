<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNotGuardian
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->hasRole('guardian') && !$user->hasRole(['admin', 'general_manager', 'manager', 'supervisor', 'teacher'])) {
            // ✅ AJAX — بدون رسالة
            if ($request->expectsJson()) {
                return response()->json([], 403);
            }

            // ✅ يرجع لآخر صفحة بدون أي رسالة
            $previous = url()->previous();
            $current  = url()->current();

            if ($previous && $previous !== $current) {
                return redirect()->to($previous);
            }

            return redirect()->route('guardian.dashboard');
        }

        return $next($request);
    }
}
