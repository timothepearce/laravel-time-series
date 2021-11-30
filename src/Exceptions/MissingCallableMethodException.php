<?php

namespace TimothePearce\Quasar\Exceptions;

use Exception;

class MissingCallableMethodException extends Exception
{
    protected $message = "Missing callable method from Projection.";
}
