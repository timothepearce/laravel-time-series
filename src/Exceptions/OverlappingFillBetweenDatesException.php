<?php

namespace TimothePearce\TimeSeries\Exceptions;

use Exception;

class OverlappingFillBetweenDatesException extends Exception
{
    protected $message = "The `fillBetween()` dates are overlapping.";
}
