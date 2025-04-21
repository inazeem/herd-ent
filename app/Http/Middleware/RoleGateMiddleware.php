<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleGateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (Auth::guest()) {
            abort(403, 'Unauthorized action.');
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        foreach ($roles as $role) {
            if (Auth::user()->hasRole(trim($role))) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
} 