<?php

namespace Database\Factories;

use App\Models\User;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            User::EMAIL => fake()->unique()->safeEmail(),
        ];
    }

    public function email(string $email): self
    {
        return $this->state([User::EMAIL => $email]);
    }

    public function hasCredit(float|BigDecimal $credit): self
    {
        return $this->state([User::CREDIT => BigDecimal::of($credit)]);
    }
}
