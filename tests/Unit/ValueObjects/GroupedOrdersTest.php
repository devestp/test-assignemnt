<?php

namespace Tests\Unit\ValueObjects;

use Brick\Math\BigDecimal;
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
                price: BigDecimal::of($price),
                count: $count
            ),
        ]);

        $vo = new GroupedOrders($orders);

        $this->assertCount(1, $vo->getGroups());
        $this->assertTrue($vo->getGroups()->first()->getPrice()->isEqualTo($price));
        $this->assertEquals($count, $vo->getGroups()->first()->getCount());
    }
}
