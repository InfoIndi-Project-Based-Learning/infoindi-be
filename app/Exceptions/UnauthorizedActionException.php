<?php

namespace App\Exceptions;

class UnauthorizedActionException extends \Exception
{
    public function __construct()
    {
        parent::__construct('You are not authorized to perform this action.', 403);
    }
}
