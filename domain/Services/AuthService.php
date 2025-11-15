<?php

namespace Domain\Services;

use Domain\Entities\User;

interface AuthService
{
    /**
     * Returns the currently authenticated user.
     *
     * If no user is authenticated, it will return null.
     */
    public function currentUser(): ?User;
}
