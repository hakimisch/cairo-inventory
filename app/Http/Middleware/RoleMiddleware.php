<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Check if the authenticated user has the exact required role.
     *
     * Usage: middleware('role:admin')
     *        middleware('role:superadmin')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== $role) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Halaman ini untuk ' . $role . ' sahaja.');
        }

        return $next($request);
    }
}
