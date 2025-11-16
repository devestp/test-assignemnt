<?php

namespace Domain\Entities;

use Brick\Math\BigDecimal;
use Domain\Enum\OrderType;

class Order
{
    public function __construct(
        private readonly int $id,
        private readonly int $userId,
        private readonly BigDecimal $amount,
        private readonly BigDecimal $price,
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

    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    public function getPrice(): BigDecimal
    {
        return $this->price;
    }

    public function getType(): OrderType
    {
        return $this->type;
    }
}
