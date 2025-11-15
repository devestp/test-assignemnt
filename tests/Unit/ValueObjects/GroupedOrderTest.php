<?php

namespace Tests\Unit\ValueObjects;

use Domain\ValueObjects\GroupedOrder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('value objects')]
class GroupedOrderTest extends TestCase
{
    public function test_it_creates()
    {
        $price = 1000;
        $count = 10;

        $vo = new GroupedOrder(
            price: $price,
            count: $count,
        );

        $this->assertEquals($price, $vo->getPrice());
        $this->assertEquals($count, $vo->getCount());
    }
}
