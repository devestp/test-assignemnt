<?php

namespace Tests\Unit\Entities;

use Domain\Entities\Order;
use Domain\Enum\OrderType;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('entities')]
class OrderTest extends TestCase
{
    public function test_it_creates()
    {
        $id = 1;
        $userId = 2;
        $amount = 10;
        $price = 100;
        $type = OrderType::SELL;

        $user = new Order(
            id: $id,
            userId: $userId,
            amount: $amount,
            price: $price,
            type: $type,
        );

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($userId, $user->getUserId());
        $this->assertEquals($amount, $user->getAmount());
        $this->assertEquals($price, $user->getPrice());
        $this->assertEquals($type, $user->getType());
    }
}
