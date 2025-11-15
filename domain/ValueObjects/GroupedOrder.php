<?php

namespace Domain\ValueObjects;

readonly class GroupedOrder
{
    public function __construct(
        private float $price,
        private int $count,
    ) {}

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
