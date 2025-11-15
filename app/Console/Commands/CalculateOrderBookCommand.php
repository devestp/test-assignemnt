<?php

namespace App\Console\Commands;

use Domain\Aggregates\OrderBook\CalculateOrderBook;
use Illuminate\Console\Command;

class CalculateOrderBookCommand extends Command
{
    protected $signature = 'app:calculate-order-book';

    protected $description = 'Calculates the order book';

    public function handle(CalculateOrderBook $calculateOrderBook): void
    {
        $calculateOrderBook->handle();
    }
}
