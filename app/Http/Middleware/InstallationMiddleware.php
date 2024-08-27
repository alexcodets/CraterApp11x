<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Models\Setting;
use Illuminate\Http\Request;
use Schema;

class InstallationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! \Storage::disk('local')->exists('database_created')) {
            return redirect('/on-boarding');
        }

        if (\Storage::disk('local')->exists('database_created')) {
            if (! Schema::hasTable('settings') || Setting::getSetting('profile_complete') !== 'COMPLETED') {
                return redirect('/on-boarding');
            }
        }

        return $next($request);
    }
}
