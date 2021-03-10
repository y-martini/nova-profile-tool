<?php

namespace YuriyMartini\Nova\Tools\Profile;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use YuriyMartini\Nova\Tools\Profile\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'profile');

        $this->app->booted(function () {
            $this->routes();
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if (App::routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('/profile')
            ->namespace('YuriyMartini\Nova\Tools\Profile\Http\Controllers')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
