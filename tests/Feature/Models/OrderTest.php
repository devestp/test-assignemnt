<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\User;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('models')]
class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_with_only_required_values()
    {
        $user = User::factory()->create();
        $amount = 10;
        $price = 11;
        $type = OrderType::BUY;
        $idempotencyToken = now()->getTimestamp();
        $state = OrderState::PENDING;

        $order = Order::create([
            Order::USER_ID => $user->getKey(),
            Order::AMOUNT => $amount,
            Order::PRICE => $price,
            Order::TYPE => $type,
            Order::IDEMPOTENCY_TOKEN => $idempotencyToken,
            Order::STATE => $state,
        ]);

        $this->assertEquals($user->getKey(), $order->user_id);
        $this->assertEquals($amount, $order->amount);
        $this->assertEquals($price, $order->price);
        $this->assertEquals($type, $order->type);
        $this->assertEquals($idempotencyToken, $order->idempotency_token);
        $this->assertEquals($state, $order->state);
        $this->assertNull($order->matched_order_id);
    }

    public function test_it_creates_with_all_values()
    {
        $user = User::factory()->create();
        $amount = 10;
        $price = 11;
        $type = OrderType::BUY;
        $idempotencyToken = now()->getTimestamp();
        $state = OrderState::COMPLETED;
        $matchedOrder = Order::factory()->create();

        $order = Order::create([
            Order::USER_ID => $user->getKey(),
            Order::AMOUNT => $amount,
            Order::PRICE => $price,
            Order::TYPE => $type,
            Order::IDEMPOTENCY_TOKEN => $idempotencyToken,
            Order::STATE => $state,
            Order::MATCHED_ORDER_ID => $matchedOrder->getKey(),
        ]);

        $this->assertEquals($user->getKey(), $order->user_id);
        $this->assertEquals($amount, $order->amount);
        $this->assertEquals($price, $order->price);
        $this->assertEquals($type, $order->type);
        $this->assertEquals($idempotencyToken, $order->idempotency_token);
        $this->assertEquals($state, $order->state);
        $this->assertEquals($matchedOrder->getKey(), $order->matched_order_id);
    }

    public function test_is_idempotent_method_returns_true_if_it_is()
    {
        $token = '123456';
        $order = Order::factory()
            ->idempotencyToken($token)
            ->create();

        $result = $order->isIdempotent($token);

        $this->assertTrue($result);
    }

    public function test_is_idempotent_method_returns_false_if_it_is_not()
    {
        $order = Order::factory()
            ->idempotencyToken('123456')
            ->create();

        $result = $order->isIdempotent('654321');

        $this->assertFalse($result);
    }

    public function test_to_entity_method()
    {
        $order = Order::factory()->create();

        $entity = $order->toEntity();

        $this->assertEquals($order->getKey(), $entity->getId());
        $this->assertEquals($order->amount, $entity->getAmount());
        $this->assertEquals($order->price, $entity->getPrice());
        $this->assertEquals($order->type, $entity->getType());
    }
}
