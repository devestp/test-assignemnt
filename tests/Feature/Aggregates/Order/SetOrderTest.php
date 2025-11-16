<?php

namespace Tests\Feature\Aggregates\Order;

use App\Models\Order;
use App\Models\User;
use Brick\Math\BigDecimal;
use Domain\Aggregates\Order\SetOrder;
use Domain\Entities\User as UserEntity;
use Domain\Enum\OrderType;
use Domain\Exceptions\NotEnoughCreditException;
use Domain\Repositories\OrderRepository;
use Domain\Repositories\UserRepository;
use Domain\Services\AuthService;
use Domain\ValueObjects\CreateOrderData;
use Domain\ValueObjects\SetOrderData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('aggregates')]
class SetOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_exception_if_user_does_not_have_enough_credit()
    {
        $user = $this->createUserWithoutCredit();
        $this->mockAuthServiceThatReturnsUser($user);

        $this->expectException(NotEnoughCreditException::class);

        $this->setOrder(
            $this->generateValidData(),
        );
    }

    public function test_it_matches_buy_order_with_an_existing_sell_order()
    {
        $user = $this->createUserWithCredit();
        $this->mockAuthServiceThatReturnsUser($user);
        $data = $this->generateValidData(type: OrderType::BUY);
        $this->mockUserRepositoryThatSubtractsUserCredit($user, $data);
        $this->mockOrderRepositoryThatMatches($data);

        $this->setOrder($data);
    }

    public function test_it_saves_buy_order_if_no_matching_order_exists()
    {
        $user = $this->createUserWithCredit();
        $this->mockAuthServiceThatReturnsUser($user);
        $data = $this->generateValidData(type: OrderType::BUY);
        $this->mockUserRepositoryThatSubtractsUserCredit($user, $data);
        $this->mockOrderRepositoryThatDoesntMatch($data);

        $this->setOrder($data);
    }

    public function test_it_matches_sell_order_with_an_existing_buy_order()
    {
        $user = $this->createUserWithCredit();
        $this->mockAuthServiceThatReturnsUser($user);
        $data = $this->generateValidData(type: OrderType::SELL);
        $this->mockUserRepositoryThatSubtractsUserCredit($user, $data);
        $this->mockOrderRepositoryThatMatches($data);

        $this->setOrder($data);
    }

    public function test_it_saves_sell_order_if_no_matching_order_exists()
    {
        $user = $this->createUserWithCredit();
        $this->mockAuthServiceThatReturnsUser($user);
        $data = $this->generateValidData(type: OrderType::SELL);
        $this->mockUserRepositoryThatSubtractsUserCredit($user, $data);
        $this->mockOrderRepositoryThatDoesntMatch($data);

        $this->setOrder($data);
    }

    private function createUserWithoutCredit(): User
    {
        return User::factory()
            ->hasCredit(0)
            ->create();
    }

    private function createUserWithCredit(): User
    {
        return User::factory()
            ->hasCredit(10000)
            ->create();
    }

    private function mockAuthServiceThatReturnsUser(User $user): void
    {
        $this->mock(AuthService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('currentUser')
                ->once()
                ->andReturn($user->toEntity());
        });
    }

    private function mockUserRepositoryThatSubtractsUserCredit(User $user, SetOrderData $data): void
    {
        $this->mock(UserRepository::class, function (MockInterface $mock) use ($user, $data) {
            $userEntity = $user->toEntity();
            $userEntity->subtractCredit($data->getAmount()->multipliedBy($data->getPrice()));

            $mock->shouldReceive('saveCredit')
                ->once()
                ->with(Mockery::on(fn ($arg) => $this->isUserArgEqualTo($arg, $userEntity)));
        });
    }

    private function mockOrderRepositoryThatMatches(SetOrderData $data): void
    {
        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($data) {
            $order = Order::factory()
                ->type($data->getType())
                ->price($data->getPrice())
                ->amount($data->getAmount())
                ->create()
                ->toEntity();

            $mock->shouldReceive('create')
                ->once()
                ->with(Mockery::on(fn ($arg) => $this->isCreateOrderDataArgEqualToSetOrderData($arg, $data)))
                ->andReturn($order);

            $matchingOrder = Order::factory()
                ->type($data->getType()->opposite())
                ->price($data->getPrice())
                ->amount($data->getAmount())
                ->create()
                ->toEntity();

            $mock->shouldReceive('getOldestMatchingOrderTo')
                ->once()
                ->with(Mockery::isSame($order))
                ->andReturn($matchingOrder);

            $mock->shouldReceive('matchOrders')
                ->once()
                ->with(Mockery::isSame($order), Mockery::isSame($matchingOrder));
        });
    }

    private function mockOrderRepositoryThatDoesntMatch(SetOrderData $data): void
    {
        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($data) {
            $order = Order::factory()
                ->type($data->getType())
                ->price($data->getPrice())
                ->amount($data->getAmount())
                ->create()
                ->toEntity();

            $mock->shouldReceive('create')
                ->once()
                ->with(Mockery::on(fn ($arg) => $this->isCreateOrderDataArgEqualToSetOrderData($arg, $data)))
                ->andReturn($order);

            $mock->shouldReceive('getOldestMatchingOrderTo')
                ->once()
                ->with(Mockery::isSame($order))
                ->andReturn(null);

            $mock->shouldNotReceive('matchOrders');
        });
    }

    private function generateValidData(?OrderType $type = null, ?string $idempotencyToken = null): SetOrderData
    {
        return (new SetOrderData(
            type: $type ?? OrderType::BUY,
            amount: BigDecimal::of(10),
            price: BigDecimal::of(10),
        ))->additional('idempotencyToken', $idempotencyToken ?? Str::uuid());
    }

    private function setOrder(SetOrderData $data): void
    {
        /** @var SetOrder $aggregate */
        $aggregate = resolve(SetOrder::class);

        $aggregate->handle($data);
    }

    private function isCreateOrderDataArgEqualToSetOrderData(CreateOrderData $arg, SetOrderData $setOrderData): bool
    {
        return $arg->getAmount() === $setOrderData->getAmount() &&
            $arg->getType() === $setOrderData->getType() &&
            $arg->getPrice() === $setOrderData->getPrice() &&
            $arg->getAdditional() == $setOrderData->getAdditional();
    }

    private function isUserArgEqualTo(UserEntity $arg, UserEntity $user): bool
    {
        return $arg->getId() === $user->getId() &&
            $arg->getCredit()->isEqualTo($user->getCredit()) &&
            $arg->getEmail() === $user->getEmail();
    }
}
