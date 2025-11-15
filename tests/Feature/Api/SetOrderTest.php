<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\User;
use Closure;
use Database\Factories\OrderFactory;
use Domain\Aggregates\Order\SetOrder;
use Domain\Enum\OrderType;
use Domain\Exceptions\NotEnoughCreditException;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('api')]
class SetOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_is_not_authenticated()
    {
        $this->mockFunctionalityThatReceivesNothing();
        $testResponse = $this->setOrder(
            $this->generateValidData(),
        );

        $testResponse->assertUnauthorized();
        $this->assertNoOrderExists();
    }

    public function test_it_returns_forbidden_if_user_does_not_have_enough_credit()
    {
        $this->mockFunctionalityThatThrowsNotEnoughCreditException();
        $this->actingAsUser();

        $testResponse = $this->setOrder(
            $this->generateValidData(),
        );

        $testResponse->assertForbidden();
        $this->assertForbiddenReasonIsNotEnoughCredit($testResponse);
        $this->assertNoOrderExists();
    }

    public function test_it_returns_unprocessable_if_request_body_is_not_valid()
    {
        $this->mockFunctionalityThatReceivesNothing();
        $this->actingAsUser();

        foreach ($this->invalidData() as $field => $cases) {
            foreach ($cases as $data) {
                $testResponse = $this->setOrder($data);

                $testResponse->assertUnprocessable();
                $testResponse->assertOnlyJsonValidationErrors($field);
                $this->assertNoOrderExists();
            }
        }
    }

    public function test_it_returns_server_error_if_unknown_exception_happens()
    {
        $this->mockFunctionalityThatThrowsUnknownException();
        $this->actingAsUser();

        $testResponse = $this->setOrder(
            $this->generateValidData(),
        );

        $testResponse->assertInternalServerError();
        $this->assertNoOrderExists();
    }

    public function test_it_handles_idempotent_requests()
    {
        $this->mockFunctionalityThatReceivesNothing();
        $this->actingAsUser();
        $order = $this->createOrder();

        $testResponse = $this->setOrder(
            $this->generateValidData(idempotencyToken: $order->idempotency_token),
        );

        $testResponse->assertCreated();
        $this->assertOnlyOneOrderExists();
        $this->assertExactOrderExists($order);
    }

    public function test_it_handles_the_request()
    {
        $this->mockFunctionalityThatReceives();
        $this->actingAsUser();

        $testResponse = $this->setOrder(
            $this->generateValidData(),
        );

        $testResponse->assertCreated();
        $this->assertNoOrderExists();
    }

    private function mockFunctionalityThatThrowsUnknownException(): void
    {
        $this->mockFunctionality(function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new Exception('unknown exception'));
        });
    }

    private function mockFunctionalityThatThrowsNotEnoughCreditException(): void
    {
        $this->mockFunctionality(function (MockInterface $mock) {
            $user = User::factory()
                ->create()
                // Fetches default values
                ->refresh()
                ->toEntity();

            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new NotEnoughCreditException($user));
        });
    }

    private function mockFunctionalityThatReceivesNothing(): void
    {
        $this->mockFunctionality(function (MockInterface $mock) {
            $mock->shouldNotReceive('handle');
        });
    }

    private function mockFunctionalityThatReceives(): void
    {
        $this->mockFunctionality(function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once();
        });
    }

    private function mockFunctionality(Closure $closure): void
    {
        $this->mock(SetOrder::class, $closure);
    }

    private function actingAsUser(): void
    {
        Sanctum::actingAs(
            User::factory()
                ->create(),
        );
    }

    private function createOrder(?OrderType $type = null): Order
    {
        return with(Order::factory(), function (OrderFactory $factory) use ($type) {
            if (! is_null($type)) {
                $factory = $factory->type($type);
            }

            return $factory->create();
        });
    }

    private function generateValidData(?string $idempotencyToken = null): array
    {
        return [
            'type' => OrderType::BUY,
            'amount' => 10,
            'price' => 10,
            'idempotencyToken' => $idempotencyToken ?? Str::uuid(),
        ];
    }

    private function invalidData(): array
    {
        $dataBuilder = new class
        {
            private mixed $type;

            private mixed $amount;

            private mixed $price;

            private mixed $idempotencyToken;

            private array $states = [];

            public function fresh(): self
            {
                $this->type = OrderType::BUY;
                $this->amount = 10;
                $this->price = 5;
                $this->idempotencyToken = Str::uuid();
                $this->states = [];

                return $this;
            }

            public function type($value): self
            {
                $this->type = $value;

                return $this;
            }

            public function withoutType(): self
            {
                $this->states[] = function (array $data) {
                    unset($data['type']);

                    return $data;
                };

                return $this;
            }

            public function amount($value): self
            {
                $this->amount = $value;

                return $this;
            }

            public function withoutAmount(): self
            {
                $this->states[] = function (array $data) {
                    unset($data['amount']);

                    return $data;
                };

                return $this;
            }

            public function price($value): self
            {
                $this->price = $value;

                return $this;
            }

            public function withoutPrice(): self
            {
                $this->states[] = function (array $data) {
                    unset($data['price']);

                    return $data;
                };

                return $this;
            }

            public function idempotencyToken($value): self
            {
                $this->idempotencyToken = $value;

                return $this;
            }

            public function withoutIdempotencyToken(): self
            {
                $this->states[] = function (array $data) {
                    unset($data['idempotencyToken']);

                    return $data;
                };

                return $this;
            }

            public function toArray(): array
            {
                $data = [
                    'type' => $this->type,
                    'amount' => $this->amount,
                    'price' => $this->price,
                    'idempotencyToken' => $this->idempotencyToken,
                ];

                foreach ($this->states as $state) {
                    $data = $state($data);
                }

                return $data;
            }
        };

        return [
            'type' => [
                $dataBuilder->fresh()->withoutType()->toArray(),
                $dataBuilder->fresh()->type(1)->toArray(),
                $dataBuilder->fresh()->type('1')->toArray(),
                $dataBuilder->fresh()->type(null)->toArray(),
            ],
            'amount' => [
                $dataBuilder->fresh()->withoutAmount()->toArray(),
                $dataBuilder->fresh()->amount(-1)->toArray(),
                $dataBuilder->fresh()->amount('abcdefg')->toArray(),
                $dataBuilder->fresh()->amount(null)->toArray(),
            ],
            'price' => [
                $dataBuilder->fresh()->withoutPrice()->toArray(),
                $dataBuilder->fresh()->price(-1)->toArray(),
                $dataBuilder->fresh()->price('abcdefg')->toArray(),
                $dataBuilder->fresh()->price(null)->toArray(),
            ],
            'idempotencyToken' => [
                $dataBuilder->fresh()->withoutIdempotencyToken()->toArray(),
                $dataBuilder->fresh()->idempotencyToken(-1)->toArray(),
                $dataBuilder->fresh()->idempotencyToken('abcdefg')->toArray(),
                $dataBuilder->fresh()->idempotencyToken(null)->toArray(),
            ],
        ];
    }

    private function setOrder(array $data): TestResponse
    {
        return $this->postJson(
            route('api.order.set'),
            $data,
        );
    }

    private function assertExactOrderExists(Order $order): void
    {
        $this->assertDatabaseHas(Order::class, [
            Order::USER_ID => $order->user_id,
            Order::AMOUNT => $order->amount,
            Order::PRICE => $order->price,
            Order::TYPE => $order->type,
            Order::IDEMPOTENCY_TOKEN => $order->idempotency_token,
            Order::STATE => $order->state,
        ]);
    }

    private function assertForbiddenReasonIsNotEnoughCredit(TestResponse $testResponse): void
    {
        $testResponse->assertJsonStructure([
            'reason',
        ]);

        $this->assertEquals('notEnoughCredit', $testResponse->json('reason'));
    }

    private function assertOnlyOneOrderExists(): void
    {
        $this->assertDatabaseCount(Order::class, 1);
    }

    private function assertNoOrderExists(): void
    {
        $this->assertDatabaseCount(Order::class, 0);
    }
}
