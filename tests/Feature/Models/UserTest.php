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

    public function test_it_creates()
    {
        $email = 'test@test.com';

        $user = User::create([
            User::EMAIL => $email,
        ]);

        $this->assertEquals($email, $user->email);
    }
}
