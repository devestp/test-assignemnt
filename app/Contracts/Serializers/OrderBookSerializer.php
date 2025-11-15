<?php

namespace App\Contracts\Serializers;

use Domain\ValueObjects\OrderBook;

/**
 * Transforms the order book.
 *
 * Rationale:
 * Serialization and deserialization are intentionally excluded from the
 * domain value object. These concerns fall outside the domain layer’s
 * responsibility and are therefore delegated to the implementation layer
 * to maintain proper separation of concerns and architectural integrity.
 */
interface OrderBookSerializer
{
    public function serialize(OrderBook $orderBook): array;

    public function deserialize(array $serializedOrderBook): OrderBook;
}
