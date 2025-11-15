<?php

namespace App\Repositories;

use App\Models\User as UserModel;
use Domain\Entities\User;
use Domain\Repositories\UserRepository;

class UserRepositoryImpl implements UserRepository
{
    public function saveCredit(User $user): void
    {
        UserModel::query()
            ->whereKey($user->getId())
            ->lockForUpdate()
            ->update([
                UserModel::CREDIT => $user->getCredit(),
            ]);
    }
}
