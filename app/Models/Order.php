<?php

namespace App\Models;

use Core\Database\Eloquent\Model;
use Core\Idempotency\Concerns\HasIdempotencyCheck;
use Core\Idempotency\Contracts\ChecksIdempotency;
use Database\Factories\OrderFactory;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $user_id
 * @property-read float $amount
 * @property-read float $price
 * @property-read OrderType $type
 * @property-read string $idempotency_token
 * @property-read OrderState $state
 * @property-read ?int $matched_order_id
 */
class Order extends Model implements ChecksIdempotency
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    use HasIdempotencyCheck;

    public const USER_ID = 'user_id';

    public const AMOUNT = 'amount';

    public const PRICE = 'price';

    public const TYPE = 'type';

    public const IDEMPOTENCY_TOKEN = 'idempotency_token';

    public const STATE = 'state';

    public const MATCHED_ORDER_ID = 'matched_order_id';

    protected $fillable = [
        self::USER_ID,
        self::AMOUNT,
        self::PRICE,
        self::TYPE,
        self::IDEMPOTENCY_TOKEN,
        self::STATE,
        self::MATCHED_ORDER_ID,
    ];

    protected $casts = [
        self::STATE => OrderState::class,
        self::TYPE => OrderType::class,
    ];

    public function getIdempotencyKey(): string
    {
        return self::IDEMPOTENCY_TOKEN;
    }
}
