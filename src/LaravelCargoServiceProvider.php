<?php

namespace Laravelcargo\LaravelCargo;

use Laravelcargo\LaravelCargo\Commands\LaravelCargoCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCargoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cargo')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-cargo_table')
            ->hasCommand(LaravelCargoCommand::class);
    }
}
