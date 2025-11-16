<?php

namespace Tests\Unit\Entities;

use Brick\Math\BigDecimal;
use Domain\Entities\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('entities')]
class UserTest extends TestCase
{
    public function test_it_creates()
    {
        $id = 1;
        $email = 'test@exmaple.com';
        $credit = 100;

        $user = new User(
            id: $id,
            email: $email,
            credit: BigDecimal::of($credit),
        );

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertTrue($user->getCredit()->isEqualTo($credit));
    }

    public function test_has_credit_method_returns_true_if_has()
    {
        $user = $this->createUser();

        $this->assertTrue(
            $user->hasCredit(100),
        );
    }

    public function test_has_credit_method_returns_true_if_doesnt_have()
    {
        $user = $this->createUser();

        $this->assertFalse(
            $user->hasCredit(1100),
        );
    }

    public function test_subtract_from_credit_method()
    {
        $user = $this->createUser();

        $user->subtractCredit(100);

        $this->assertTrue($user->getCredit()->isEqualTo(900));
    }

    private function createUser(): User
    {
        return new User(
            id: 1,
            email: 'test@exmaple.com',
            credit: BigDecimal::of(1000),
        );
    }
}
