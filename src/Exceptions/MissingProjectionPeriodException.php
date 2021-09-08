<?php

namespace Laravelcargo\LaravelCargo\Exceptions;

use Exception;

class MissingProjectionPeriodException extends Exception
{
    protected $message = "The projection's period is missing from you query.";
}
