<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
//    public function map()
//    {
//        $this->mapApiRoutes();
//
//        $this->mapWebRoutes();
//
//        //
//    }
    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix('api')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/reportRoute.php'));

        Route::prefix('api')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/webApi.php'));

        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    public static function roleMethod(): array
    {
        return ['auth', 'company', 'webJson'];

    }
}
