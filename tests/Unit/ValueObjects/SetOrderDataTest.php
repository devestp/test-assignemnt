<?php

namespace Tests\Unit\ValueObjects;

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

        $vo = new SetOrderData(type: $type, amount: $amount, price: $price);

        $this->assertEquals($type, $vo->getType());
        $this->assertEquals($amount, $vo->getAmount());
        $this->assertEquals($price, $vo->getPrice());
    }
}
