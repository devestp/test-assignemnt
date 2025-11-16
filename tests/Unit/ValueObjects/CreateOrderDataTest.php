<?php

namespace Tests\Unit\ValueObjects;

use Brick\Math\BigDecimal;
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
            amount: BigDecimal::of($amount),
            price: BigDecimal::of($price),
        );

        $this->assertEquals($userId, $vo->getUserId());
        $this->assertEquals($type, $vo->getType());
        $this->assertTrue($vo->getAmount()->isEqualTo($amount));
        $this->assertTrue($vo->getPrice()->isEqualTo($price));
    }
}
