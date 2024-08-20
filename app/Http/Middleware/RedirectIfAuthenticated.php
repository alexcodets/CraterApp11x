<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            if ($request->is('approve-estimates/*')) {
                return $next($request);
            }

            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
