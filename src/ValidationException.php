<?php

namespace Andileong\Validation;

use Throwable;

class ValidationException extends \Exception
{

    private $errors;

    public function __construct($errors, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function errors()
    {
        return $this->errors;
    }

}