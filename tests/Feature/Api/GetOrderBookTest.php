<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Closure;
use Domain\Aggregates\OrderBook\GetOrderBook;
use Domain\ValueObjects\OrderBook;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('api')]
class GetOrderBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unauthorized_if_user_is_not_authenticated()
    {
        $this->mockFunctionalityThatReceivesNothing();
        $testResponse = $this->getOrderBook();

        $testResponse->assertUnauthorized();
    }

    public function test_it_returns_server_error_if_unknown_exception_happens()
    {
        $this->mockFunctionalityThatThrowsUnknownException();
        $this->actingAsUser();

        $testResponse = $this->getOrderBook();

        $testResponse->assertInternalServerError();
    }

    public function test_it_handles_the_request()
    {
        $this->mockFunctionalityThatReceives();
        $this->actingAsUser();

        $testResponse = $this->getOrderBook();

        $testResponse->assertOk();
        $this->assertResponseStructure($testResponse);
    }

    private function mockFunctionalityThatThrowsUnknownException(): void
    {
        $this->mockFunctionality(function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new Exception('unknown exception'));
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
                ->once()
                ->andReturn(OrderBook::empty());
        });
    }

    private function mockFunctionality(Closure $closure): void
    {
        $this->mock(GetOrderBook::class, $closure);
    }

    private function actingAsUser(): void
    {
        Sanctum::actingAs(
            User::factory()
                ->create(),
        );
    }

    private function getOrderBook(): TestResponse
    {
        return $this->getJson(
            route('api.order-book')
        );
    }

    private function assertResponseStructure(TestResponse $testResponse): void
    {
        $testResponse->assertJsonStructure([
            'orderBook' => [
                'buyOrders' => [
                    '*' => [
                        'price',
                        'count',
                    ],
                ],
                'sellOrders' => [
                    '*' => [
                        'price',
                        'count',
                    ],
                ],
            ],
        ]);
    }
}
