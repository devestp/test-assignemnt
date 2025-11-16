<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory()
            ->count(3)
            ->hasCredit(100000000000)
            ->create();

        $token = $users->first()->createToken('auth')->plainTextToken;

        $this->command->info("Test User Api Token: $token");
    }
}
