<?php

namespace Domain\Enum;

use Core\Support\Enumerable;

enum OrderState: string
{
    use Enumerable;

    case PENDING = 'pending';
    case COMPLETED = 'completed';
}
