<?php

namespace Domain\Aggregates\OrderBook;

use Domain\Repositories\OrderBookRepository;
use Domain\Repositories\OrderRepository;
use Domain\ValueObjects\OrderBook;

readonly class CalculateOrderBook
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderBookRepository $orderBookRepository,
    ) {}

    public function handle(): void
    {
        $this->orderBookRepository->save(
            new OrderBook(
                groupedBuyOrders: $this->orderRepository->getPendingBuysForOrderBook(),
                groupedSellOrders: $this->orderRepository->getPendingSellsForOrderBook()
            ),
        );
    }
}
