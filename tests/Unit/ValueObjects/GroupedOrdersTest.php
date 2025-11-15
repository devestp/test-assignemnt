<?php

namespace Tests\Unit\ValueObjects;

use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('value objects')]
class GroupedOrdersTest extends TestCase
{
    public function test_it_creates()
    {
        $price = 10;
        $count = 1;
        $orders = collect([
            new GroupedOrder(
                price: $price,
                count: $count
            ),
        ]);

        $vo = new GroupedOrders($orders);

        $this->assertCount(1, $vo->getGroups());
        $this->assertEquals($price, $vo->getGroups()->first()->getPrice());
        $this->assertEquals($count, $vo->getGroups()->first()->getCount());
    }
}
