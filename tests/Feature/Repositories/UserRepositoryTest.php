<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use Brick\Math\BigDecimal;
use Domain\Entities\User as UserEntity;
use Domain\Exceptions\EntityNotFoundException;
use Domain\Repositories\UserRepository;
use Domain\ValueObjects\Id;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('repositories')]
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_or_fail_by_id_for_update_throws_exception_if_user_does_not_exist()
    {
        $id = new Id(1);
        $repo = $this->createRepository();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage(UserEntity::class." entity not found with id $id");

        $repo->findOrFailByIdForUpdate($id);
    }

    public function test_find_or_fail_by_id_for_update_finds_the_user()
    {
        $user = $this->createUser();
        $id = new Id($user->getKey());
        $repo = $this->createRepository();

        $result = $repo->findOrFailByIdForUpdate($id);

        $this->assertEquals($user->getKey(), $result->getId());
    }

    public function test_save_method_saves_the_user_credit()
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
