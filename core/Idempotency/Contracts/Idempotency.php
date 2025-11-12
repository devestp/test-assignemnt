<?php

namespace Core\Idempotency\Contracts;

/**
 * The idempotency service contract.
 */
interface Idempotency
{
    public function check(string $provider, string $token): bool;
}
