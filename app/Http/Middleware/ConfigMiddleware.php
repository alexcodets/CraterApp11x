<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Models\CompanySetting;
use Crater\Models\FileDisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfigMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\Storage::disk('local')->has('database_created')) {

            $seconds = 10 * 60;
            $setting = null;
            if ($request->header('company')) {
                $setting = Cache::remember('config_time_zone_'.$request->header('company'), $seconds, function () use ($request) {
                    return CompanySetting::getSetting('time_zone', $request->header('company'));
                });
            }

            $timezone = config('app.timezone');

            if ($setting && $setting != $timezone) {
                config(['app.timezone' => $setting]);
                date_default_timezone_set($setting);
            }

            if ($request->has('file_disk_id')) {
                $file_disk = Cache::remember('file_disk_'.$request->file_disk_id, $seconds, function () use ($request) {
                    return FileDisk::find($request->file_disk_id);
                });
            } else {
                $file_disk = Cache::remember('file_disk_d', $seconds, function () {
                    return FileDisk::where('set_as_default', true)->first() ?? false;
                });
            }

            if ($file_disk) {
                $file_disk->setConfig();
            }
        }

        return $next($request);
    }
}
