<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class AdminMiddleware
{
    /**
     * Only allow users with role = 'admin' through.
     * Everyone else gets redirected to the dashboard with a flash message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Halaman ini untuk pentadbir sahaja.');
        }
 
        return $next($request);
    }
}