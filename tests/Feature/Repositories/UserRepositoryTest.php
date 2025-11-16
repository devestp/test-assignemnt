<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use Brick\Math\BigDecimal;
use Domain\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('repositories')]
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_the_user_credit()
    {
        $amount = 123.345;
        $user = $this->createUser();
        $userEntity = $user->toEntity();
        $userEntity->subtractCredit(BigDecimal::of($amount));
        $repo = $this->createRepository();

        $repo->saveCredit($userEntity);

        $user->refresh();
        $this->assertTrue($user->credit->isEqualTo(1000 - $amount));
    }

    private function createUser(): User
    {
        return User::factory()
            ->hasCredit(1000)
            ->create()
            ->refresh();
    }

    private function createRepository(): UserRepository
    {
        return resolve(UserRepository::class);
    }
}
