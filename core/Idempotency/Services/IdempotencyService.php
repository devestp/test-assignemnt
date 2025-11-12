<?php

namespace Core\Idempotency\Services;

use Core\Idempotency\Contracts\ChecksIdempotency;
use Core\Idempotency\Contracts\Idempotency;
use InvalidArgumentException;

/**
 * The implementation of the idempotency service.
 */
class IdempotencyService implements Idempotency
{
    public function check(string|ChecksIdempotency $provider, string $token): bool
    {
        if (is_string($provider)) {
            $provider = new $provider;
        }

        if (! ($provider instanceof ChecksIdempotency)) {
            throw new InvalidArgumentException(
                'Provider must implement '.ChecksIdempotency::class,
            );
        }

        return $provider->isIdempotent($token);
    }
}
