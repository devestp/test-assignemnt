<?php

namespace Core\Idempotency\Concerns;

/**
 * Adds necessary logic for eloquent models to become an idempotency provider.
 */
trait HasIdempotencyCheck
{
    public function isIdempotent(string $token): bool
    {
        return self::query()
            ->where($this->getIdempotencyKey(), $token)
            ->exists();
    }

    /**
     * Should return the property key that is used for idempotency checks.
     */
    abstract public function getIdempotencyKey(): string;
}
