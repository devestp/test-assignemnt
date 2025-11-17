<?php

namespace Domain\Exceptions;

use Exception;
use Throwable;

/**
 * Base class for all domain related exceptions.
 */
abstract class DomainException extends Exception
{
    /**
     * @param  string  $message  The message is required to make sure all exceptions have a meaningful message.
     */
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(
            message: $message,
            previous: $previous,
        );
    }
}
