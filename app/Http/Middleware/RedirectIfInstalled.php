<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Models\Setting;
use Illuminate\Http\Request;
use Storage;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (Storage::disk('local')->exists('database_created') && Setting::getSetting('profile_complete') === 'COMPLETED') {
            return redirect('login');
        }

        return $next($request);
    }
}
