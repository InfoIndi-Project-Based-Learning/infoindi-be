<?php

namespace App\Exceptions;

class UserBannedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Your account has been suspended.', 403);
    }
}
