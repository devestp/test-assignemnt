<?php

namespace Domain\Repositories;

use Domain\Entities\Order;
use Domain\ValueObjects\CreateOrderData;
use Domain\ValueObjects\GroupedOrders;

interface OrderRepository
{
    /**
     * Creates new order entity.
     */
    public function createForUpdate(CreateOrderData $data): Order;

    /**
     * Gets all pending buy orders grouped and sorted by the price.
     *
     * The prices should be ordered by the price in descending order.
     */
    public function getPendingBuysForOrderBook(): GroupedOrders;

    /**
     * Gets all pending sell orders grouped and sorted by the price.
     *
     * The prices should be ordered by the price in ascending order.
     */
    public function getPendingSellsForOrderBook(): GroupedOrders;

    /**
     * Gets the oldest matching order to given order otherwise null.
     */
    public function getOldestMatchingOrderForUpdate(Order $order): ?Order;

    /**
     * Matches two orders with each other.
     */
    public function matchOrders(Order $first, Order $second): void;
}
