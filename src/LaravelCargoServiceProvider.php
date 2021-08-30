<?php

namespace Laravelcargo\LaravelCargo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Laravelcargo\LaravelCargo\Commands\LaravelCargoCommand;

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
