<?php

namespace Domain\ValueObjects;

readonly class OrderBook
{
    public function __construct(
        private GroupedOrders $groupedBuyOrders,
        private GroupedOrders $groupedSellOrders,
    ) {}

    public static function empty(): static
    {
        return new self(
            groupedBuyOrders: new GroupedOrders(collect()),
            groupedSellOrders: new GroupedOrders(collect())
        );
    }

    public function getGroupedBuyOrders(): GroupedOrders
    {
        return $this->groupedBuyOrders;
    }

    public function getGroupedSellOrders(): GroupedOrders
    {
        return $this->groupedSellOrders;
    }
}
