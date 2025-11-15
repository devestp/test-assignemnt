<?php

namespace App\Casts;

use App\Contracts\Serializers\OrderBookSerializer;
use Domain\ValueObjects\OrderBook;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

readonly class OrderBookCast implements CastsAttributes
{
    private OrderBookSerializer $orderBookSerializer;

    public function __construct()
    {
        // Because casts are not handled by the container, we have to
        // resolve the serializer manually.
        // Keep the resolve statement in the constructor to be more
        // detectable by readers.
        $this->orderBookSerializer = resolve(OrderBookSerializer::class);
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): OrderBook
    {
        if (is_string($value)) {
            $value = json_decode($value, associative: true);
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException('$value must be an array, '.gettype($value).' given.');
        }

        return $this->orderBookSerializer->deserialize($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! ($value instanceof OrderBook)) {
            throw new InvalidArgumentException('$value must be an '.OrderBook::class);
        }

        return json_encode(
            $this->orderBookSerializer->serialize($value),
        );
    }
}
