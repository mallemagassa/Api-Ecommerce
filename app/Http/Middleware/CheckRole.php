<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole{

    /**
     * Handle an incoming request
     * 
     * @param \Closure(\illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response{
        $user = $request->user();

        if (! $user || ! in_array($user->isSeller, $role)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}