<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('models')]
class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_with_only_required_values()
    {
        $email = 'test@test.com';

        $user = User::create([
            User::EMAIL => $email,
        ]);

        $user->refresh();
        $this->assertEquals($email, $user->email);
        $this->assertTrue($user->credit->isZero());
    }

    public function test_it_creates_with_all_values()
    {
        $email = 'test@test.com';
        $credit = 10.123456;

        $user = User::create([
            User::EMAIL => $email,
            User::CREDIT => $credit,
        ]);

        $user->refresh();
        $this->assertEquals($email, $user->email);
        $this->assertTrue($user->credit->isEqualTo($credit));
    }

    public function test_to_entity_method()
    {
        $user = User::factory()->create();
        // Loads default values
        $user->refresh();

        $entity = $user->toEntity();

        $this->assertEquals($user->getKey(), $entity->getId());
        $this->assertEquals($user->email, $entity->getEmail());
        $this->assertEquals($user->credit, $entity->getCredit());
    }
}
