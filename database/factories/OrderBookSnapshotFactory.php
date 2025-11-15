<?php

namespace Database\Factories;

use App\Models\OrderBookSnapshot;
use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Domain\ValueObjects\OrderBook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderBookSnapshot>
 */
class OrderBookSnapshotFactory extends Factory
{
    public function definition(): array
    {
        return [
            OrderBookSnapshot::DATA => new OrderBook(
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
            ),
        ];
    }
}
