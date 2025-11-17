<?php

namespace Domain\Repositories;

use Domain\Entities\User;
use Domain\Exceptions\EntityNotFoundException;
use Domain\ValueObjects\Id;

interface UserRepository
{
    /**
     * Finds a user by id for update.
     *
     * @throws EntityNotFoundException
     */
    public function findOrFailByIdForUpdate(Id $id): User;

    /**
     * Persists the user credit.
     */
    public function saveCredit(User $user): void;
}
