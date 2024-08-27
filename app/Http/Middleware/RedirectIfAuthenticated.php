<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string|null $guard = null)
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
