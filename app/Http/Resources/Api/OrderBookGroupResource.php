<?php

namespace App\Http\Resources\Api;

use Domain\ValueObjects\GroupedOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read GroupedOrder $resource
 */
class OrderBookGroupResource extends JsonResource
{
    private const PRICE = 'price';

    private const COUNT = 'count';

    public function toArray(Request $request): array
    {
        return [
            self::PRICE => $this->resource->getPrice(),
            self::COUNT => $this->resource->getCount(),
        ];
    }
}
