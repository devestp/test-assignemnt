<?php

namespace Domain\ValueObjects;

use Domain\Concerns\HasAdditionalData;
use Domain\Enum\OrderType;

class SetOrderData
{
    use HasAdditionalData;

    public function __construct(
        private readonly OrderType $type,
        private readonly float $amount,
        private readonly float $price,
    ) {}

    public function getType(): OrderType
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
