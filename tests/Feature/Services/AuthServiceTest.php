<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Services\AuthServiceImpl;
use Domain\Services\AuthService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('services')]
class AuthServiceTest extends TestCase
{
    public function test_it_returns_the_current_user_if_authenticated()
    {
        $user = $this->createUser();
        $this->actingAs($user);
        $service = $this->createService();

        $result = $service->currentUser();

        $this->assertEquals($user->getKey(), $result->getId());
    }

    public function test_it_returns_null_if_authenticated()
    {
        $service = $this->createService();

        $result = $service->currentUser();

        $this->assertNull($result);
    }

    private function createUser(): User
    {
        return User::factory()
            ->create()
            ->refresh();
    }

    private function createService(): AuthService
    {
        return new AuthServiceImpl;
    }
}
