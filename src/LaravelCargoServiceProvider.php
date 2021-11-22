<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Support\ServiceProvider;
use Laravelcargo\LaravelCargo\Commands\CreateProjectionCommand;

class LaravelCargoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/cargo.php' => config_path('cargo.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateProjectionCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cargo.php',
            'cargo'
        );
    }
}
