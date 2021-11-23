<?php

namespace TimothePearce\Quasar\Exceptions;

use Exception;

class MissingProjectionPeriodException extends Exception
{
    protected $message = "The projection's period is missing from you query.";
}
