<?php

namespace Tests\Feature\Models;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('models')]
class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_with_only_required_values()
    {
        $email = 'test@test.com';
        $password = Hash::make('password');

        $admin = Admin::create([
            Admin::EMAIL => $email,
            Admin::PASSWORD => $password,
        ]);

        $this->assertEquals($email, $admin->email);
        $this->assertTrue(Hash::check('password', $admin->password));
    }
}
