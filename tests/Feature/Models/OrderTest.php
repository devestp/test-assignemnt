<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\User;
use Domain\Enum\OrderState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('models')]
class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates()
    {
        $user = User::factory()->create();
        $amount = 10;
        $price = 11;
        $idempotencyToken = now()->getTimestamp();
        $state = OrderState::PENDING;

        $order = Order::create([
            Order::USER_ID => $user->getKey(),
            Order::AMOUNT => $amount,
            Order::PRICE => $price,
            Order::IDEMPOTENCY_TOKEN => $idempotencyToken,
            Order::STATE => $state,
        ]);

        $this->assertEquals($user->getKey(), $order->user_id);
        $this->assertEquals($amount, $order->amount);
        $this->assertEquals($price, $order->price);
        $this->assertEquals($idempotencyToken, $order->idempotency_token);
        $this->assertEquals($state, $order->state);
    }
}
