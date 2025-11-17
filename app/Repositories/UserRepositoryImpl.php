<?php

namespace App\Repositories;

use App\Models\User as UserModel;
use Domain\Entities\User;
use Domain\Exceptions\EntityNotFoundException;
use Domain\Repositories\UserRepository;
use Domain\ValueObjects\Id;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepositoryImpl implements UserRepository
{
    public function findOrFailByIdForUpdate(Id $id): User
    {
        try {
            return $this->tryFindOrFailByIdForUpdate($id);
        } catch (ModelNotFoundException $e) {
            throw new EntityNotFoundException(entity: User::class, id: $id, previous: $e);
        }
    }

    public function saveCredit(User $user): void
    {
        UserModel::query()
            ->whereKey($user->getId())
            ->update([
                UserModel::CREDIT => $user->getCredit(),
            ]);
    }

    private function tryFindOrFailByIdForUpdate(Id $id): User
    {
        return UserModel::query()
            ->lockForUpdate()
            ->whereKey($id->value())
            ->firstOrFail()
            ->toEntity();
    }
}
