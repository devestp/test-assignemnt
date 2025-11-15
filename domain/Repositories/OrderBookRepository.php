<?php

namespace Domain\Repositories;

use Domain\ValueObjects\OrderBook;

interface OrderBookRepository
{
    /**
     * Persists the given order book.
     */
    public function save(OrderBook $orderBook): void;

    /**
     * Gets the order book from persistent storage.
     */
    public function get(): OrderBook;
}
