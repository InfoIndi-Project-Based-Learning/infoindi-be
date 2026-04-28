<?php

namespace App\Exceptions;

class UserAlreadyExistException extends \Exception
{
    public function __construct()
    {
        parent::__construct('User already exist.', 409);
    }
}