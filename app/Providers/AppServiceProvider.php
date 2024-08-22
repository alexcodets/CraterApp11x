<?php

namespace Crater\Providers;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Models\BandwidthAccount;
use Crater\Models\Setting;
use Crater\Models\User;
use Crater\Services\Bandwidth\BandwidthService;
use Crater\Services\Bandwidth\DataTransferObjects\AccountDTO;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/admin/dashboard';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (! Schema::hasTable('settings') || User::withTrashed()->count() == 0) {
            return;
        }

        Model::preventLazyLoading(!$this->app->isProduction());

        Paginator::useBootstrapThree();
        $this->loadJsonTranslationsFrom(resource_path('assets/js/plugins'));
        $value = Setting::all();
        foreach ($value as $key) {
            if ($key['option'] == 'company_page_title') {
                $data = $key['value'];
                View::share('key', $data);
            }
        }

        $user = User::first();

        if ($user){
            $user->load([
                'company',
            ]);

            if ($user['company']) {
                $data = $user['company']['favicon'];
                View::share('img', $data);
            }
        }

        $this->bootAuth();
        $this->bootBroadcast();
        $this->bootRoute();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AvalaraService::class, function ($app) {
            return new AvalaraService(new AvalaraApi());
        });

        $this->app->singleton(AvalaraApi::class, function ($app) {
            return new AvalaraApi();
        });

        $this->app->singleton(BandwidthService::class, function ($app) {
            return new BandwidthService(new AccountDTO(BandwidthAccount::active()->selected()->first()->toService()));
        });

    }

    public function bootAuth()
    {
        // Implicitly grant "admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }

    public function bootBroadcast()
    {
        Broadcast::routes(['middleware' => 'api.auth']);
    }

    public function bootRoute()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });


    }
}
