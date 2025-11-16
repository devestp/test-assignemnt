<?php

namespace App\Http\Resources\Api;

use Domain\ValueObjects\OrderBook;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read OrderBook $resource
 *
 * @method static self make(OrderBook $orderBook)
 */
class OrderBookResource extends JsonResource
{
    private const ORDER_BOOK = 'orderBook';

    private const SELL_ORDERS = 'sellOrders';

    private const BUY_ORDERS = 'buyOrders';

    public function toArray(Request $request): array
    {
        return [
            self::ORDER_BOOK => [
                self::BUY_ORDERS => OrderBookGroupResource::collection(
                    $this->resource->getGroupedBuyOrders()->getGroups(),
                ),
                self::SELL_ORDERS => OrderBookGroupResource::collection(
                    $this->resource->getGroupedSellOrders()->getGroups(),
                ),
            ],
        ];
    }
}
