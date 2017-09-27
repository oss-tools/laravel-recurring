<?php

namespace BlessingDube\Recurring\Exceptions;

use Exception;
use Throwable;

class UnknownFrequencyException extends Exception
{
    public function __construct($message = 'The chosen frequency is unknown', $code = 422, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
