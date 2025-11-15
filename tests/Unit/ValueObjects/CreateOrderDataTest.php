<?php

namespace Tests\Unit\ValueObjects;

use Domain\Enum\OrderType;
use Domain\ValueObjects\CreateOrderData;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('value-objects')]
class CreateOrderDataTest extends TestCase
{
    public function test_its_methods()
    {
        $userId = 1;
        $type = OrderType::BUY;
        $amount = 10;
        $price = 1;

        $vo = new CreateOrderData(
            userId: $userId,
            type: $type,
            amount: $amount,
            price: $price,
        );

        $this->assertEquals($userId, $vo->getUserId());
        $this->assertEquals($type, $vo->getType());
        $this->assertEquals($amount, $vo->getAmount());
        $this->assertEquals($price, $vo->getPrice());
    }
}
