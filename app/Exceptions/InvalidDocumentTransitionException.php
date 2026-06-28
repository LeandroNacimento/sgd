<?php

namespace App\Exceptions;

use Exception;

class InvalidDocumentTransitionException extends Exception
{
    public function __construct(string $fromState, string $toState)
    {
        parent::__construct("Cannot transition document from '{$fromState}' to '{$toState}'.");
    }
}
