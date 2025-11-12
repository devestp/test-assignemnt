<?php

namespace Core\Support;

use BackedEnum;
use Illuminate\Support\Collection;

/**
 * Adds utility functions to Backed enums.
 */
trait Enumerable
{
    public static function values(): Collection
    {
        return collect(self::cases())
            ->map(fn (BackedEnum $enum) => $enum->value);
    }
}
