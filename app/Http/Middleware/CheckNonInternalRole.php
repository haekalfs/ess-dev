<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckNonInternalRole
{
    public function handle(Request $request, Closure $next)
    {
        //should be changed to use Env
        // Check if the user is authenticated and has the 'non-internal' role
        if (auth()->check() && in_array('non-internal', auth()->user()->role_id()->pluck('role_name')->toArray())) {
            // Check if the user is already in the correct domain
            if (!str_contains($request->url(), 'partner-hub.perdana.co.id')) {
                // Redirect the user to a specific domain
                return redirect()->away('https://partner-hub.perdana.co.id');
            }
        }

        return $next($request);
    }
}
