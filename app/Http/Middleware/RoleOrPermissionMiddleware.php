<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware as SpatieRoleOrPermissionMiddleware;

class RoleOrPermissionMiddleware
{
    protected $middleware;

    public function __construct()
    {
        $this->middleware = new SpatieRoleOrPermissionMiddleware();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roleOrPermission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roleOrPermission)
    {
        return $this->middleware->handle($request, $next, $roleOrPermission);
    }
} 