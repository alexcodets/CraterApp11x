<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Barryvdh\DomPDF\ServiceProvider::class,
        \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        \Spatie\Permission\PermissionServiceProvider::class,
        \Mavinoo\Batch\BatchServiceProvider::class,
        \Maatwebsite\Excel\ExcelServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->validateCsrfTokens(except: [
            'api/*',
            'v1/*',
            'api/v1/*',
            'login'
        ]);

        $middleware->append([
            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
            \Crater\Http\Middleware\TrimStrings::class,
            \Crater\Http\Middleware\TrustProxies::class,
            \Crater\Http\Middleware\ConfigMiddleware::class,
        ]);

        $middleware->web([
            \Crater\Http\Middleware\EncryptCookies::class,
            \Crater\Http\Middleware\VerifyCsrfToken::class,
        ]);

        $middleware->statefulApi();
        $middleware->throttleApi('180,1');

        $middleware->replaceInGroup('web', \Illuminate\Cookie\Middleware\EncryptCookies::class, \Crater\Http\Middleware\EncryptCookies::class);

        $middleware->alias([
            'admin' => \Crater\Http\Middleware\AdminMiddleware::class,
            'auth' => \Crater\Http\Middleware\Authenticate::class,
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'commonuser' => \Crater\Http\Middleware\CommonuserMiddleware::class,
            'customer' => \Crater\Http\Middleware\CustomerMiddleware::class,
            'guest' => \Crater\Http\Middleware\RedirectIfAuthenticated::class,
            'install' => \Crater\Http\Middleware\InstallationMiddleware::class,
            'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'redirect-if-installed' => \Crater\Http\Middleware\RedirectIfInstalled::class,
            'redirect-if-unauthenticated' => \Crater\Http\Middleware\RedirectIfUnauthorized::class,
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Crater\Http\Middleware\Authenticate::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
