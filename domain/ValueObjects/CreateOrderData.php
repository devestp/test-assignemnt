<?php

namespace Domain\ValueObjects;

use Domain\Concerns\HasAdditionalData;
use Domain\Enum\OrderType;

class CreateOrderData
{
    use HasAdditionalData;

    public function __construct(
        private readonly int $userId,
        private readonly OrderType $type,
        private readonly float $amount,
        private readonly float $price,
    ) {}

    public function getUserId(): int
    {
        return $this->userId;
    }

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
