<?php

namespace TimothePearce\Quasar;

use Illuminate\Support\ServiceProvider;
use TimothePearce\Quasar\Commands\CreateProjectionCommand;
use TimothePearce\Quasar\Commands\ProjectModelsCommand;

class QuasarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/quasar.php' => config_path('quasar.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateProjectionCommand::class,
                ProjectModelsCommand::class,
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
            __DIR__.'/../config/quasar.php',
            'cargo'
        );
    }
}
