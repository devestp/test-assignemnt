<?php

namespace App\Models;

use Core\Database\Eloquent\Model;
use Database\Factories\OrderFactory;
use Domain\Enum\OrderState;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $user_id
 * @property-read float $amount
 * @property-read float $price
 * @property-read string $idempotency_token
 * @property-read OrderState $state
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const USER_ID = 'user_id';

    public const AMOUNT = 'amount';

    public const PRICE = 'price';

    public const IDEMPOTENCY_TOKEN = 'idempotency_token';

    public const STATE = 'state';

    protected $fillable = [
        self::USER_ID,
        self::AMOUNT,
        self::PRICE,
        self::IDEMPOTENCY_TOKEN,
        self::STATE,
    ];

    protected $casts = [
        self::STATE => OrderState::class,
    ];
}
