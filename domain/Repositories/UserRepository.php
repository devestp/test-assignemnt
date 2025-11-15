<?php

namespace Domain\Repositories;

use Domain\Entities\User;

interface UserRepository
{
    /**
     * Persists the user credit.
     *
     * A separate function for this purpose is created to allow
     * the implementation to use locking.
     */
    public function saveCredit(User $user): void;
}
