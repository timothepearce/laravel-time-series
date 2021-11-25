<?php

namespace TimothePearce\Quasar\Exceptions;

use Exception;

class MissingProjectionNameException extends Exception
{
    protected $message = "The projection's name is missing from you query.";
}
