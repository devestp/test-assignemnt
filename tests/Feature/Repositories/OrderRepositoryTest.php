<?php

namespace Tests\Feature\Repositories;

use App\Models\Order;
use App\Models\User;
use Brick\Math\BigDecimal;
use Domain\Entities\Order as OrderEntity;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Domain\Repositories\OrderRepository;
use Domain\ValueObjects\CreateOrderData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use Ramsey\Uuid\UuidInterface;
use Tests\TestCase;

#[Group('repositories')]
class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_method_creates_order()
    {
        $user = User::factory()->create();
        $type = OrderType::BUY;
        $amount = 10;
        $price = 5;
        $idempotencyToken = Str::uuid();
        $data = (new CreateOrderData(
            userId: $user->getKey(),
            type: $type,
            amount: BigDecimal::of($amount),
            price: BigDecimal::of($price),
        ))->additional('idempotencyToken', $idempotencyToken);
        $repo = $this->createRepository();

        $createdOrder = $repo->create($data);

        $this->assertOneOrderExists();
        $this->assertExactOrderExists($createdOrder, $idempotencyToken);
    }

    public function test_get_pending_buys_for_order_book_method_returns_empty_if_no_order_exists()
    {
        $repo = $this->createRepository();

        $result = $repo->getPendingBuysForOrderBook();

        $this->assertCount(0, $result->getGroups());
    }

    public function test_get_pending_buys_for_order_book_method_returns_empty_if_orders_are_not_pending()
    {
        $this->createCompletedBuyOrders();
        $repo = $this->createRepository();

        $result = $repo->getPendingBuysForOrderBook();

        $this->assertCount(0, $result->getGroups());
    }

    public function test_get_pending_buys_for_order_book_method_returns_empty_if_orders_are_sell()
    {
        $this->createPendingSellOrders();
        $repo = $this->createRepository();

        $result = $repo->getPendingBuysForOrderBook();

        $this->assertCount(0, $result->getGroups());
    }

    public function test_get_pending_buys_for_order_book_method_returns_correct_results()
    {
        $this->createPendingBuyOrders();
        $repo = $this->createRepository();

        $result = $repo->getPendingBuysForOrderBook();

        $this->assertCount(2, $result->getGroups());
        // First group
        $this->assertTrue(
            $result->getGroups()->first()->getPrice()->isEqualTo(15),
        );
        $this->assertEquals(
            2,
            $result->getGroups()->first()->getCount()
        );
        // Second group
        $this->assertTrue(
            $result->getGroups()->skip(1)->first()->getPrice()->isEqualTo(8),
        );
        $this->assertEquals(
            3,
            $result->getGroups()->skip(1)->first()->getCount()
        );
    }

    public function test_get_pending_sells_for_order_book_method_returns_empty_if_no_order_exists()
    {
        $repo = $this->createRepository();

        $result = $repo->getPendingSellsForOrderBook();

        $this->assertCount(0, $result->getGroups());
    }

    public function test_get_pending_sells_for_order_book_method_returns_empty_if_orders_are_not_pending()
    {
        $this->createCompletedSellOrders();
        $repo = $this->createRepository();

        $result = $repo->getPendingSellsForOrderBook();

        $this->assertCount(0, $result->getGroups());
    }

    public function test_get_pending_sells_for_order_book_method_returns_empty_if_orders_are_buy()
    {
        $this->createPendingBuyOrders();
        $repo = $this->createRepository();

        $result = $repo->getPendingSellsForOrderBook();

        $this->assertCount(0, $result->getGroups());
    }

    public function test_get_pending_sells_for_order_book_method_returns_correct_results()
    {
        $this->createPendingSellOrders();
        $repo = $this->createRepository();

        $result = $repo->getPendingSellsForOrderBook();

        $this->assertCount(2, $result->getGroups());
        // First group
        $this->assertTrue(
            $result->getGroups()->first()->getPrice()->isEqualTo(4),
        );
        $this->assertEquals(
            2,
            $result->getGroups()->first()->getCount()
        );
        // Second group
        $this->assertTrue(
            $result->getGroups()->skip(1)->first()->getPrice()->isEqualTo(10),
        );
        $this->assertEquals(
            3,
            $result->getGroups()->skip(1)->first()->getCount()
        );
    }

    public function test_get_oldest_matching_order_to_method_returns_null_if_no_other_order_exists()
    {
        $repo = $this->createRepository();

        $result = $repo->getOldestMatchingOrderTo(
            $this->createOrder()->toEntity(),
        );

        $this->assertNull($result);
    }

    public function test_get_oldest_matching_order_to_method_returns_null_if_order_is_on_the_same_side()
    {
        $order = $this->createBuyOrder();
        $this->replicateOrder($order);
        $repo = $this->createRepository();

        $result = $repo->getOldestMatchingOrderTo(
            $order->toEntity(),
        );

        $this->assertNull($result);
    }

    public function test_get_oldest_matching_order_to_method_returns_null_if_amount_is_not_the_same()
    {
        $order = $this->createBuyOrder();
        $this->replicateOrder($order, amount: $order->amount->plus(10));
        $repo = $this->createRepository();

        $result = $repo->getOldestMatchingOrderTo(
            $order->toEntity(),
        );

        $this->assertNull($result);
    }

    public function test_get_oldest_matching_order_to_returns_null_if_price_is_not_the_same()
    {
        $order = $this->createBuyOrder();
        $this->replicateOrder($order, price: $order->price->plus(10));
        $repo = $this->createRepository();

        $result = $repo->getOldestMatchingOrderTo(
            $order->toEntity(),
        );

        $this->assertNull($result);
    }

    public function test_get_oldest_matching_order_to_returns_null_if_state_is_not_pending()
    {
        $order = $this->createBuyOrder();
        $this->replicateOrder($order, state: OrderState::COMPLETED);
        $repo = $this->createRepository();

        $result = $repo->getOldestMatchingOrderTo(
            $order->toEntity(),
        );

        $this->assertNull($result);
    }

    public function test_get_oldest_matching_order_to_returns_the_oldest_matching_order()
    {
        $this->freezeTime();
        $first = $this->createOldPendingBuyOrder();
        // extra orders
        $this->createBuyOrder();
        $this->createBuyOrder();

        $second = $this->replicateOrder($first, type: OrderType::SELL);
        $repo = $this->createRepository();

        $result = $repo->getOldestMatchingOrderTo(
            $second->toEntity(),
        );

        $this->assertEquals($first->getKey(), $result->getId());
    }

    public function test_match_orders_method_matches_them_with_each_other()
    {
        $first = $this->createBuyOrder();
        $second = $this->replicateOrder($first, type: OrderType::SELL);
        $repo = $this->createRepository();

        $repo->matchOrders($first->toEntity(), $second->toEntity());

        $this->assertOrdersAreMatched($first, $second);
    }

    private function createOrder(): Order
    {
        return Order::factory()
            ->create();
    }

    private function createOldPendingBuyOrder(): Order
    {
        return Order::factory()
            ->buy()
            ->old()
            ->pending()
            ->create();
    }

    private function createBuyOrder(): Order
    {
        return Order::factory()
            ->buy()
            ->create();
    }

    private function createCompletedBuyOrders(): void
    {
        Order::factory()
            ->completed()
            ->buy()
            ->count(4)
            ->create();
    }

    private function createCompletedSellOrders(): void
    {
        Order::factory()
            ->completed()
            ->sell()
            ->count(4)
            ->create();
    }

    private function createPendingBuyOrders(): void
    {
        Order::factory()
            ->pending()
            ->buy()
            ->price(8)
            ->count(3)
            ->create();

        Order::factory()
            ->pending()
            ->buy()
            ->price(15)
            ->count(2)
            ->create();
    }

    private function createPendingSellOrders(): void
    {
        Order::factory()
            ->pending()
            ->sell()
            ->price(10)
            ->count(3)
            ->create();

        Order::factory()
            ->pending()
            ->sell()
            ->price(4)
            ->count(2)
            ->create();
    }

    private function replicateOrder(
        Order $order,
        ?OrderType $type = null,
        ?BigDecimal $amount = null,
        ?BigDecimal $price = null,
        ?OrderState $state = null,
    ): Order {
        $new = $order->replicate();

        if (! is_null($type)) {
            $new->setAttribute(Order::TYPE, $type);
        }

        if (! is_null($amount)) {
            $new->setAttribute(Order::AMOUNT, $amount);
        }

        if (! is_null($price)) {
            $new->setAttribute(Order::PRICE, $price);
        }

        if (! is_null($state)) {
            $new->setAttribute(Order::STATE, $state);
        }

        $new->save();

        return $new;
    }

    private function createRepository(): OrderRepository
    {
        return resolve(OrderRepository::class);
    }

    private function assertOneOrderExists(): void
    {
        $this->assertDatabaseCount(Order::class, 1);
    }

    private function assertExactOrderExists(OrderEntity $order, UuidInterface $idempotencyToken): void
    {
        $this->assertDatabaseHas(Order::class, [
            Order::USER_ID => $order->getUserId(),
            Order::TYPE => $order->getType(),
            Order::AMOUNT => $order->getAmount(),
            Order::PRICE => $order->getPrice(),
            Order::IDEMPOTENCY_TOKEN => $idempotencyToken,
        ]);
    }

    private function assertOrdersAreMatched(Order $first, Order $second): void
    {
        $first->refresh();
        $this->assertEquals(OrderState::COMPLETED, $first->state);
        $this->assertEquals($second->getKey(), $first->matched_order_id);

        $second->refresh();
        $this->assertEquals(OrderState::COMPLETED, $second->state);
        $this->assertEquals($first->getKey(), $second->matched_order_id);
    }
}
