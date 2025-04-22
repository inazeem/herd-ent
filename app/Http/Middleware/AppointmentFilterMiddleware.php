<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class AppointmentFilterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Apply default filters if none are provided
        if (!$request->has('filter')) {
            // Default to upcoming appointments if no filter is specified
            $request->merge(['filter' => 'upcoming']);
        }

        // Apply specific filter logic
        if ($request->filter === 'today') {
            $request->merge([
                'date_from' => Carbon::today()->format('Y-m-d'),
                'date_to' => Carbon::today()->format('Y-m-d'),
            ]);
        } elseif ($request->filter === 'upcoming') {
            $request->merge([
                'date_from' => Carbon::today()->format('Y-m-d'),
            ]);
        } elseif ($request->filter === 'this_week') {
            $request->merge([
                'date_from' => Carbon::today()->format('Y-m-d'),
                'date_to' => Carbon::today()->endOfWeek()->format('Y-m-d'),
            ]);
        } elseif ($request->filter === 'this_month') {
            $request->merge([
                'date_from' => Carbon::today()->format('Y-m-d'),
                'date_to' => Carbon::today()->endOfMonth()->format('Y-m-d'),
            ]);
        } elseif ($request->filter === 'completed') {
            $request->merge(['status' => 'completed']);
        } elseif ($request->filter === 'cancelled') {
            $request->merge(['status' => 'cancelled']);
        } elseif ($request->filter === 'no_show') {
            $request->merge(['status' => 'no-show']);
        }

        return $next($request);
    }
}
