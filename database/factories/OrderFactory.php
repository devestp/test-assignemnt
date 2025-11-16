<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Brick\Math\BigDecimal;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            Order::USER_ID => User::factory(),
            Order::AMOUNT => fake()->randomNumber(),
            Order::PRICE => fake()->randomNumber(),
            Order::TYPE => fake()->randomElement(OrderType::values()),
            Order::IDEMPOTENCY_TOKEN => fake()->uuid(),
            Order::STATE => fake()->randomElement(OrderState::values()),
        ];
    }

    public function idempotencyToken(string $token): self
    {
        return $this->state([Order::IDEMPOTENCY_TOKEN => $token]);
    }

    public function type(OrderType $type): self
    {
        return $this->state([Order::TYPE => $type]);
    }

    public function buy(): self
    {
        return $this->type(OrderType::BUY);
    }

    public function sell(): self
    {
        return $this->type(OrderType::SELL);
    }

    public function old(): self
    {
        return $this->state(function () {
            return [
                Order::CREATED_AT => now()->subDays(
                    fake()->randomNumber(2),
                ),
            ];
        });
    }

    public function amount(int|float|BigDecimal $amount): self
    {
        return $this->state([Order::AMOUNT => $amount]);
    }

    public function price(int|float|BigDecimal $price): self
    {
        return $this->state([Order::PRICE => $price]);
    }

    public function completed(): self
    {
        return $this->state([Order::STATE => OrderState::COMPLETED]);
    }

    public function pending(): self
    {
        return $this->state([Order::STATE => OrderState::PENDING]);
    }

    public function priceBetween(int|float|BigDecimal $min, int|float|BigDecimal $max): self
    {
        return $this->state(function () use ($min, $max) {
            return [
                Order::PRICE => fake()->randomFloat(min: $min, max: $max),
            ];
        });
    }

    public function amountBetween(int|float|BigDecimal $min, int|float|BigDecimal $max): self
    {
        return $this->state(function () use ($min, $max) {
            return [
                Order::AMOUNT => fake()->randomFloat(min: $min, max: $max),
            ];
        });
    }
}
