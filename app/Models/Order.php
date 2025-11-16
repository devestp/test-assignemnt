<?php

namespace App\Models;

use App\Casts\BigDecimalCast;
use Brick\Math\BigDecimal;
use Core\Database\Eloquent\Model;
use Core\Idempotency\Concerns\HasIdempotencyCheck;
use Core\Idempotency\Contracts\ChecksIdempotency;
use Database\Factories\OrderFactory;
use Domain\Entities\Order as OrderEntity;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read int $user_id
 * @property-read BigDecimal $amount
 * @property-read BigDecimal $price
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
        self::PRICE => BigDecimalCast::class,
        self::AMOUNT => BigDecimalCast::class,
        self::STATE => OrderState::class,
        self::TYPE => OrderType::class,
    ];

    public function getIdempotencyKey(): string
    {
        return self::IDEMPOTENCY_TOKEN;
    }

    public function toEntity(): OrderEntity
    {
        return new OrderEntity(
            id: $this->getKey(),
            userId: $this->user_id,
            amount: $this->amount,
            price: $this->price,
            type: $this->type,
        );
    }
}
