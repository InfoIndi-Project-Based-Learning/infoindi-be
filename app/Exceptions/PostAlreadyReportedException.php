<?php

namespace App\Exceptions;

class PostAlreadyReportedException extends \Exception
{
    public function __construct()
    {
        parent::__construct('You have already reported this post.', 409);
    }
}
