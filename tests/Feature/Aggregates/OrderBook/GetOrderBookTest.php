<?php

namespace Tests\Feature\Aggregates\OrderBook;

use Domain\Aggregates\OrderBook\GetOrderBook;
use Domain\Repositories\OrderBookRepository;
use Domain\ValueObjects\OrderBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class GetOrderBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_order_book()
    {
        $this->mockOrderBookRepositoryThatReceives();

        $this->getOrderBook();
    }

    private function mockOrderBookRepositoryThatReceives(): void
    {
        $this->mock(OrderBookRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->once()
                ->andReturn(OrderBook::empty());
        });
    }

    private function getOrderBook(): OrderBook
    {
        /** @var GetOrderBook $aggregate */
        $aggregate = resolve(GetOrderBook::class);

        return $aggregate->handle();
    }
}
