<?php

namespace Laravelcargo\LaravelCargo\Exceptions;

use Exception;

class MissingParametersOnEmptyProjectionCollectionException extends Exception
{
    protected $message = "Impossible to guess the projector name or period on empty projections collection.";
}
