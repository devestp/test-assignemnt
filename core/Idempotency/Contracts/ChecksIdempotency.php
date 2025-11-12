<?php

namespace Core\Idempotency\Contracts;

/**
 * Interface for an idempotency provider.
 */
interface ChecksIdempotency
{
    public function isIdempotent(string $token): bool;
}
