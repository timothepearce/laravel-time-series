<?php

namespace TimothePearce\TimeSeries\Exceptions;

use Exception;

class EmptyProjectionCollectionException extends Exception
{
    protected $message = "Impossible to resolve the projector name or period on empty projections collection.";
}
