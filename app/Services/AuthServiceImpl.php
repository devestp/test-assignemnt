<?php

namespace App\Services;

use Domain\Services\AuthService;
use Domain\ValueObjects\Id;
use Illuminate\Support\Facades\Auth;

class AuthServiceImpl implements AuthService
{
    public function currentUserId(): ?Id
    {
        if (! Auth::check()) {
            return null;
        }

        return new Id(
            Auth::id(),
        );
    }
}
