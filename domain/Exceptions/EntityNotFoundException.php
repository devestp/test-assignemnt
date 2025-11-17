<?php

namespace Domain\Exceptions;

use Domain\ValueObjects\Id;
use Throwable;

class EntityNotFoundException extends DomainException
{
    /**
     * @param  string  $entity  The entity trying to find
     */
    public function __construct(string $entity, Id $id, ?Throwable $previous = null)
    {
        parent::__construct(
            message: "$entity entity not found with id $id",
            previous: $previous,
        );
    }
}
