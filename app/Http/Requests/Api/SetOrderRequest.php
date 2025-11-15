<?php

namespace App\Http\Requests\Api;

use Domain\Enum\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetOrderRequest extends FormRequest
{
    private const AMOUNT = 'amount';

    private const PRICE = 'price';

    private const TYPE = 'type';

    const IDEMPOTENCY_TOKEN = 'idempotencyToken';

    public function rules(): array
    {
        return [
            self::AMOUNT => [
                'required',
                'decimal:0,6',
                'min:1',
            ],
            self::PRICE => [
                'required',
                'decimal:0,6',
                'min:1',
            ],
            self::TYPE => [
                'required',
                Rule::enum(OrderType::class),
            ],
            self::IDEMPOTENCY_TOKEN => [
                'required',
                'uuid',
            ],
        ];
    }

    public function getAmount(): float
    {
        return $this->validated(self::AMOUNT);
    }

    public function getPrice(): float
    {
        return $this->validated(self::PRICE);
    }

    public function getType(): OrderType
    {
        return OrderType::from(
            $this->validated(self::TYPE),
        );
    }

    public function getIdempotencyToken(): string
    {
        return $this->validated(self::IDEMPOTENCY_TOKEN);
    }
}
