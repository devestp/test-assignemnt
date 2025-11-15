<?php

namespace Tests\Unit\Serializers;

use App\Serializers\OrderBookSerializerImpl;
use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Domain\ValueObjects\OrderBook;
use PHPUnit\Framework\TestCase;

class OrderBookSerializerTest extends TestCase
{
    public function test_it_serializes_order_book()
    {
        $orderBook = $this->createOrderBook();
        $serializer = $this->createSerializer();

        $result = $serializer->serialize($orderBook);

        $this->assertOrderBookIsSerialized($orderBook, $result);
    }

    public function test_it_deserializes_order_book()
    {
        $serializedOrderBook = $this->createSerializedOrderBook();
        $serializer = $this->createSerializer();

        $result = $serializer->deserialize($serializedOrderBook);

        $this->assertOrderBookIsDeserialized($serializedOrderBook, $result);
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

    private function createSerializedOrderBook(): array
    {
        return [
            'buy_orders' => [
                [
                    'price' => 10.5,
                    'count' => 2,
                ],
                [
                    'price' => 10,
                    'count' => 15,
                ],
                [
                    'price' => 9.2,
                    'count' => 1,
                ],
            ],
            'sell_orders' => [
                [
                    'price' => 9.1,
                    'count' => 5,
                ],
                [
                    'price' => 9.8,
                    'count' => 5,
                ],
                [
                    'price' => 10,
                    'count' => 15,
                ],
            ],
        ];
    }

    private function createSerializer(): OrderBookSerializerImpl
    {
        return new OrderBookSerializerImpl;
    }

    private function assertOrderBookIsSerialized(OrderBook $orderBook, array $serializedOrderBook): void
    {
        $this->assertGroupedOrdersEqual($orderBook->getGroupedBuyOrders(), $serializedOrderBook['buy_orders']);
        $this->assertGroupedOrdersEqual($orderBook->getGroupedSellOrders(), $serializedOrderBook['sell_orders']);
    }

    private function assertOrderBookIsDeserialized(array $serializedOrderBook, OrderBook $orderBook): void
    {
        $this->assertGroupedOrdersEqual($orderBook->getGroupedBuyOrders(), $serializedOrderBook['buy_orders']);
        $this->assertGroupedOrdersEqual($orderBook->getGroupedSellOrders(), $serializedOrderBook['sell_orders']);
    }

    private function assertGroupedOrdersEqual(GroupedOrders $groupedOrders, array $serializedGroupedOrders): void
    {
        $groups = $groupedOrders->getGroups();

        for ($i = 0; $i < count($groups); $i++) {
            $this->assertGroupedOrderEquals($groups[$i], $serializedGroupedOrders[$i]);
        }
    }

    private function assertGroupedOrderEquals(GroupedOrder $groupedOrder, array $serializedGroupedOrder): void
    {
        $this->assertEquals($groupedOrder->getCount(), $serializedGroupedOrder['count']);
        $this->assertEquals($groupedOrder->getPrice(), $serializedGroupedOrder['price']);
    }
}
