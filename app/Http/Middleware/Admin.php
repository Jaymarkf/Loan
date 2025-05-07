<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // If user is authenticated and trying to access guest-only routes, redirect to dashboard
        if (Auth::check() && in_array($path, ['/', 'login'])) {
            return redirect('/dashboard');
        }

        // If user is not authenticated and trying to access protected routes, block
        if (!Auth::check() && !in_array($path, ['/', 'login'])) {
            abort(403);
        }

        // Allow the request
        return $next($request);
    }
}
