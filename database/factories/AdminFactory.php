<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            Admin::EMAIL => fake()->unique()->email(),
            Admin::PASSWORD => Hash::make('password'),
            Admin::REMEMBER_TOKEN => Str::random(),
        ];
    }
}
