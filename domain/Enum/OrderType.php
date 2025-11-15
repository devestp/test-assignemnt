<?php

namespace Domain\Enum;

use Core\Support\Enumerable;

enum OrderType: string
{
    use Enumerable;

    case BUY = 'buy';
    case SELL = 'sell';

    public function isSell(): bool
    {
        return $this === self::SELL;
    }

    public function opposite(): OrderType
    {
        return $this->isSell() ? self::BUY : self::SELL;
    }
}
