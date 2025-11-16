<?php

namespace App\Casts;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class BigDecimalCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): BigDecimal
    {
        if (! $this->isValueTypeValid($value)) {
            throw new InvalidArgumentException(
                '$value must be an instance of '.BigDecimal::class.', int, float or string. '.gettype($value).' given.'
            );
        }

        return BigDecimal::of($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $this->isValueTypeValid($value)) {
            throw new InvalidArgumentException(
                '$value must be an instance of '.BigDecimal::class.', int, float or string. '.gettype($value).' given.'
            );
        }

        return (string) BigDecimal::of($value)
            ->toScale(6, RoundingMode::HALF_UP);
    }

    private function isValueTypeValid(mixed $value): bool
    {
        return $value instanceof BigDecimal || is_float($value) || is_int($value) || is_numeric($value);
    }
}
