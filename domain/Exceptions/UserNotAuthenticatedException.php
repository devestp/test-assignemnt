<?php

namespace Domain\Exceptions;

use Throwable;

class UserNotAuthenticatedException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('No user is authenticated.', $previous);
    }
}
