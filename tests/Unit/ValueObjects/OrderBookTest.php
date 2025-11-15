<?php

namespace Tests\Unit\ValueObjects;

use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Domain\ValueObjects\OrderBook;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('value objects')]
class OrderBookTest extends TestCase
{
    public function test_it_creates()
    {
        $orders = new GroupedOrders(
            collect([
                new GroupedOrder(
                    price: 10,
                    count: 1,
                ),
            ]),
        );

        $vo = new OrderBook(
            groupedBuyOrders: $orders,
            groupedSellOrders: $orders,
        );

        $this->assertCount(1, $vo->getGroupedBuyOrders()->getGroups());
        $this->assertCount(1, $vo->getGroupedSellOrders()->getGroups());
    }
}
