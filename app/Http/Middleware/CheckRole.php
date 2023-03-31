<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();
        $userRoles = $user->role()->whereIn('role_name', $roles)->get();

        if ($userRoles->count() > 0) {
            return $next($request);
        }
        Session::flash('failed',"You doesn't have rights to access this page!");
        return redirect('/'); // or return a response with an error message
    }

}
