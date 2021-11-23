<?php

namespace TimothePearce\Quasar\Exceptions;

use Exception;

class MissingProjectorNameException extends Exception
{
    protected $message = "The projection's name is missing from you query.";
}
