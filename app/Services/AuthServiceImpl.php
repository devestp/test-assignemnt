<?php

namespace App\Services;

use App\Models\User as UserModel;
use Domain\Entities\User;
use Domain\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthServiceImpl implements AuthService
{
    public function currentUser(): ?User
    {
        /** @var UserModel|null $user */
        $user = Auth::user();

        return $user?->toEntity();
    }
}
