<?php

namespace Laravelcargo\LaravelCargo\Exceptions;

use Exception;

class EmptyProjectionCollectionException extends Exception
{
    protected $message = "Impossible to guess the projector name or period on empty projections collection.";
}
