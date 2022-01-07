<?php

namespace TimothePearce\Quasar;

use Illuminate\Support\ServiceProvider;
use TimothePearce\Quasar\Commands\CreateProjectionCommand;
use TimothePearce\Quasar\Commands\DropProjectionsCommand;
use TimothePearce\Quasar\Commands\ProjectModelsCommand;

class QuasarServiceProvider extends ServiceProvider
{
    /**
     * Bootstraps the package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/quasar.php' => config_path('quasar.php'),
        ], 'quasar-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateProjectionCommand::class,
                DropProjectionsCommand::class,
                ProjectModelsCommand::class,
            ]);
        }
    }

    /**
     * Registers any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/quasar.php',
            'quasar'
        );

        $this->app->singleton(Quasar::class, function () {
            return new Quasar();
        });
    }
}
