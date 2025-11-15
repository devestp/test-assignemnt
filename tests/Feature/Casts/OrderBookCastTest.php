<?php

namespace Feature\Casts;

use App\Casts\OrderBookCast;
use App\Contracts\Serializers\OrderBookSerializer;
use Domain\ValueObjects\OrderBook;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('casts')]
class OrderBookCastTest extends TestCase
{
    public function test_get_method_throws_exception_if_value_is_not_an_array()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$value must be an array, integer given.');

        $this->runGet(1);
    }

    public function test_get_method_converts_to_order_book()
    {
        $serializedOrderBook = $this->createSerializedOrderBook();
        $this->mockOrderBookSerializerThatReceivesDeserialize($serializedOrderBook);

        $this->runGet($serializedOrderBook);
    }

    public function test_set_method_throws_exception_if_value_is_not_an_order_book()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$value must be an '.OrderBook::class);

        $this->runSet(1);
    }

    public function test_set_method_converts_to_string()
    {
        $orderBook = $this->createOrderBook();
        $this->mockOrderBookSerializerThatReceivesSerialize($orderBook);

        $this->runSet($orderBook);
    }

    private function createSerializedOrderBook(): array
    {
        return [
            'buy_orders' => [],
            'sell_orders' => [],
        ];
    }

    private function createOrderBook(): OrderBook
    {
        return OrderBook::empty();
    }

    private function mockOrderBookSerializerThatReceivesDeserialize(array $serializedOrderBook): void
    {
        $this->mock(OrderBookSerializer::class, function (MockInterface $mock) use ($serializedOrderBook) {
            $mock->shouldReceive('deserialize')
                ->once()
                ->with(Mockery::isEqual($serializedOrderBook))
                ->andReturn($this->createOrderBook());
        });
    }

    private function mockOrderBookSerializerThatReceivesSerialize(OrderBook $orderBook): void
    {
        $this->mock(OrderBookSerializer::class, function (MockInterface $mock) use ($orderBook) {
            $mock->shouldReceive('serialize')
                ->once()
                ->with(Mockery::isEqual($orderBook))
                ->andReturn($this->createSerializedOrderBook());
        });
    }

    private function runGet($value): OrderBook
    {
        $model = new class extends Model {};

        $cast = new OrderBookCast;

        return $cast->get($model, 'key', $value, []);
    }

    private function runSet($value): string
    {
        $model = new class extends Model {};

        $cast = new OrderBookCast;

        return $cast->set($model, 'key', $value, []);
    }
}
