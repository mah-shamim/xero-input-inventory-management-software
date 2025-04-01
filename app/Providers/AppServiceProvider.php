<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $resetPasswordUrl = env('APP_URL');
            return "{$resetPasswordUrl}/reset-password/{$token}";
        });

        if(!$this->app->environment('local')){
            LogViewer::auth(function ($request) {
                return $request->user()
                    && in_array($request->user()->email, [
                        'john@example.com',
                    ]);
            });
        }

    }
}
