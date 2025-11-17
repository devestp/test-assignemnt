<?php

namespace Domain\Services;

use Domain\ValueObjects\Id;

interface AuthService
{
    /**
     * Returns the currently authenticated user id.
     *
     * If no user is authenticated, it will return null.
     */
    public function currentUserId(): ?Id;
}
