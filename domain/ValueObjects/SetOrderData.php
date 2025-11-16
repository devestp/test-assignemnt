<?php

namespace Domain\ValueObjects;

use Brick\Math\BigDecimal;
use Domain\Concerns\HasAdditionalData;
use Domain\Enum\OrderType;

class SetOrderData
{
    use HasAdditionalData;

    public function __construct(
        private readonly OrderType $type,
        private readonly BigDecimal $amount,
        private readonly BigDecimal $price,
    ) {}

    public function getType(): OrderType
    {
        return $this->type;
    }

    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    public function getPrice(): BigDecimal
    {
        return $this->price;
    }
}
