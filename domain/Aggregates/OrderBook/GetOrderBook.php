<?php

namespace Domain\Aggregates\OrderBook;

use Domain\Repositories\OrderBookRepository;
use Domain\ValueObjects\OrderBook;

class GetOrderBook
{
    public function __construct(
        private OrderBookRepository $orderBookRepository
    ) {}

    public function handle(): OrderBook
    {
        return $this->orderBookRepository->get();
    }
}
