<?php

namespace Domain\Entities;

use Brick\Math\BigDecimal;

class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $email,
        private BigDecimal $credit,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCredit(): BigDecimal
    {
        return $this->credit;
    }

    public function hasCredit(BigDecimal|int|float $amount): bool
    {
        return $this->credit->isGreaterThanOrEqualTo($amount);
    }

    public function subtractCredit(BigDecimal|int|float $amount): void
    {
        $this->credit = $this->credit->minus($amount);
    }
}
