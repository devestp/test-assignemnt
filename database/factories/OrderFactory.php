<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Domain\Enum\OrderState;
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
            Order::IDEMPOTENCY_TOKEN => fake()->dateTime()->getTimestamp(),
            Order::STATE => fake()->randomElement(OrderState::values()),
        ];
    }
}
