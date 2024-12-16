<?php

namespace App\Exceptions;

namespace App\Exceptions;

use Exception;

class CustomValidationException extends Exception
{
    public array $validationErrors;

    public function __construct(array $validationErrors)
    {
        $this->validationErrors = $validationErrors;
        parent::__construct('Validation error');
    }
}
