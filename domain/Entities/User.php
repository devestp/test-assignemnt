<?php

namespace Domain\Entities;

class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $email,
        private float $credit,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCredit(): float
    {
        return $this->credit;
    }

    public function hasCredit(float $amount): bool
    {
        return $this->credit >= $amount;
    }

    public function subtractCredit(float $amount): void
    {
        $this->credit -= $amount;
    }
}
