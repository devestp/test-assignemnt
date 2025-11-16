<?php

namespace Domain\ValueObjects;

use Brick\Math\BigDecimal;

readonly class GroupedOrder
{
    public function __construct(
        private BigDecimal $price,
        private int $count,
    ) {}

    public function getPrice(): BigDecimal
    {
        return $this->price;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
