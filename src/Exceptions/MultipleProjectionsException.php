<?php

namespace TimothePearce\Quasar\Exceptions;

use Exception;

class MultipleProjectionsException extends Exception
{
    protected $message = "The `fillBetween()` method cannot be executed with a multiple periods projections collection.";
}
