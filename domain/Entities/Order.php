<?php

namespace Domain\Entities;

use Domain\Enum\OrderType;

class Order
{
    public function __construct(
        private readonly int $id,
        private readonly int $userId,
        private readonly float $amount,
        private readonly float $price,
        private readonly OrderType $type,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getType(): OrderType
    {
        return $this->type;
    }
}
