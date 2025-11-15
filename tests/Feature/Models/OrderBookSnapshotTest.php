<?php

namespace Tests\Feature\Models;

use App\Models\OrderBookSnapshot;
use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Domain\ValueObjects\OrderBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('models')]
class OrderBookSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates()
    {
        $data = $this->createOrderBook();

        $snapshot = OrderBookSnapshot::create([
            OrderBookSnapshot::DATA => $data,
        ]);

        $snapshot->refresh();
        $this->assertEquals($data, $snapshot->data);
    }

    private function createOrderBook(): OrderBook
    {
        return new OrderBook(
            groupedBuyOrders: new GroupedOrders(
                collect([
                    new GroupedOrder(
                        price: 10.5,
                        count: 2,
                    ),
                    new GroupedOrder(
                        price: 10,
                        count: 15,
                    ),
                    new GroupedOrder(
                        price: 9.2,
                        count: 1,
                    ),
                ])
            ),
            groupedSellOrders: new GroupedOrders(
                collect([
                    new GroupedOrder(
                        price : 9.1,
                        count : 5,
                    ),
                    new GroupedOrder(
                        price : 9.8,
                        count : 5,
                    ),
                    new GroupedOrder(
                        price : 10,
                        count : 15,
                    ),
                ])
            ),
        );
    }
}
