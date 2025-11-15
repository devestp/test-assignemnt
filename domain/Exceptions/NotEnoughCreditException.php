<?php

namespace Domain\Exceptions;

use Domain\Entities\User;
use Throwable;

class NotEnoughCreditException extends DomainException
{
    public function __construct(public readonly User $user, ?Throwable $previous = null)
    {
        parent::__construct(
            "User {$user->getId()} does not have enough credit.",
            $previous,
        );
    }
}
