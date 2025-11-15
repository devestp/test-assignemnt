<?php

namespace Tests\Feature\Aggregates\OrderBook;

use Domain\Aggregates\OrderBook\CalculateOrderBook;
use Domain\Repositories\OrderBookRepository;
use Domain\Repositories\OrderRepository;
use Domain\ValueObjects\GroupedOrders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CalculateOrderBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_order_book()
    {
        $this->mockOrderRepositoryThatReturnsEmpty();
        $this->mockOrderBookRepositoryThatReceives();

        $this->calculateOrderBook();
    }

    private function mockOrderRepositoryThatReturnsEmpty(): void
    {
        $this->mock(OrderRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('getPendingBuysForOrderBook')
                ->once()
                ->andReturn(new GroupedOrders(collect()));

            $mock->shouldReceive('getPendingSellsForOrderBook')
                ->once()
                ->andReturn(new GroupedOrders(collect()));
        });
    }

    private function mockOrderBookRepositoryThatReceives(): void
    {
        $this->mock(OrderBookRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('save')
                ->once();
        });
    }

    private function calculateOrderBook(): void
    {
        /** @var CalculateOrderBook $aggregate */
        $aggregate = resolve(CalculateOrderBook::class);

        $aggregate->handle();
    }
}
