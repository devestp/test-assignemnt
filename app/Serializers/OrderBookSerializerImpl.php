<?php

namespace App\Serializers;

use App\Contracts\Serializers\OrderBookSerializer;
use Brick\Math\BigDecimal;
use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Domain\ValueObjects\OrderBook;

class OrderBookSerializerImpl implements OrderBookSerializer
{
    private const BUY_ORDERS = 'buy_orders';

    private const SELL_ORDERS = 'sell_orders';

    private const PRICE = 'price';

    private const COUNT = 'count';

    public function serialize(OrderBook $orderBook): array
    {
        return [
            self::BUY_ORDERS => $this->serializeGroupedOrders(
                $orderBook->getGroupedBuyOrders(),
            ),
            self::SELL_ORDERS => $this->serializeGroupedOrders(
                $orderBook->getGroupedSellOrders(),
            ),
        ];
    }

    public function deserialize(array $serializedOrderBook): OrderBook
    {
        return new OrderBook(
            groupedBuyOrders: $this->deserializeGroupedOrders(
                $serializedOrderBook[self::BUY_ORDERS],
            ),
            groupedSellOrders: $this->deserializeGroupedOrders(
                $serializedOrderBook[self::SELL_ORDERS],
            ),
        );
    }

    private function serializeGroupedOrders(GroupedOrders $groupedOrders): array
    {
        return $groupedOrders->getGroups()
            ->map(fn (GroupedOrder $groupedOrder) => [
                self::PRICE => $groupedOrder->getPrice(),
                self::COUNT => $groupedOrder->getCount(),
            ])->toArray();
    }

    private function deserializeGroupedOrders(array $data): GroupedOrders
    {
        return new GroupedOrders(
            collect($data)
                ->map(fn (array $data) => new GroupedOrder(
                    price: BigDecimal::of($data[self::PRICE]),
                    count: $data[self::COUNT],
                )),
        );
    }
}
