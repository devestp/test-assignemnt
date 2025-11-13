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
        $this->assertEquals(0, $user->credit);
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
        $this->assertEquals($credit, $user->credit);
    }
}
