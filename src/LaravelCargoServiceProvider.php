<?php

namespace Laravelcargo\LaravelCargo;

use Laravelcargo\LaravelCargo\Commands\LaravelCargoCommand;
use Illuminate\Support\ServiceProvider;

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
            __DIR__.'/../config/cargo.php' => config_path('cargo.php')
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                LaravelCargoCommand::class,
            ]);
        }
    }
}
