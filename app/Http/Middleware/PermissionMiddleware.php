<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware as SpatiePermissionMiddleware;

class PermissionMiddleware
{
    protected $middleware;

    public function __construct()
    {
        $this->middleware = new SpatiePermissionMiddleware();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        return $this->middleware->handle($request, $next, $permission);
    }
} 