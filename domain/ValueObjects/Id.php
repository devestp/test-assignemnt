<?php

namespace Domain\ValueObjects;

readonly class Id
{
    public function __construct(
        private int|string $value,
    ) {}

    public function value(): int|string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
