<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || optional(auth()->user()->role)->name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
