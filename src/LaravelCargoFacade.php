<?php

namespace Laravelcargo\LaravelCargo;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laravelcargo\LaravelCargo\LaravelCargo
 */
class LaravelCargoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-cargo';
    }
}
