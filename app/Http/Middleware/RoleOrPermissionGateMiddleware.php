<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleOrPermissionGateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roleOrPermission): Response
    {
        if (Auth::guest()) {
            abort(403, 'Unauthorized action.');
        }

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        foreach ($rolesOrPermissions as $roleOrPermission) {
            if (Auth::user()->hasRole(trim($roleOrPermission)) || 
                Auth::user()->hasPermissionTo(trim($roleOrPermission))) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
} 