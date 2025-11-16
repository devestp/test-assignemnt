<?php

namespace Tests\Unit\ValueObjects;

use Brick\Math\BigDecimal;
use Domain\Enum\OrderType;
use Domain\ValueObjects\SetOrderData;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('value-objects')]
class SetOrderDataTest extends TestCase
{
    public function test_its_methods()
    {
        $type = OrderType::BUY;
        $amount = 10;
        $price = 1;

        $vo = new SetOrderData(
            type: $type,
            amount: BigDecimal::of($amount),
            price: BigDecimal::of($price),
        );

        $this->assertEquals($type, $vo->getType());
        $this->assertTrue($vo->getAmount()->isEqualTo($amount));
        $this->assertTrue($vo->getPrice()->isEqualTo($price));
    }
}
