<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Check if the authenticated user's role has the required permission.
     *
     * Usage: middleware('permission:verify')
     *        middleware('permission:view_admin')
     */
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $role = $request->user()->role;
        $roles = config("roles.{$role}");

        if (!$roles) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Peranan tidak sah.');
        }

        if (!in_array($ability, $roles['can'] ?? [])) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Anda tidak mempunyai kebenaran.');
        }

        return $next($request);
    }
}
