<?php

namespace Domain\Enum;

use Core\Support\Enumerable;

enum OrderType: string
{
    use Enumerable;

    case BUY = 'buy';
    case SELL = 'sell';
}
