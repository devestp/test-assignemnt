<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
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
            Order::IDEMPOTENCY_TOKEN => fake()->dateTime()->getTimestamp(),
            Order::STATE => fake()->randomElement(OrderState::values()),
        ];
    }

    public function idempotencyToken(string $token): self
    {
        return $this->state([Order::IDEMPOTENCY_TOKEN => $token]);
    }
}
