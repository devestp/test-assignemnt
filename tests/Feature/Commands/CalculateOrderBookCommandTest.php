<?php

namespace Tests\Feature\Commands;

use App\Console\Commands\CalculateOrderBookCommand;
use Domain\Aggregates\OrderBook\CalculateOrderBook;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('commands')]
class CalculateOrderBookCommandTest extends TestCase
{
    public function test_it_calculates()
    {
        $this->createMockFunctionalityThatReceives();

        $this->artisan(CalculateOrderBookCommand::class)
            ->assertSuccessful()
            ->expectsOutput('Calculated successfully.');
    }

    private function createMockFunctionalityThatReceives(): void
    {
        $this->mock(CalculateOrderBook::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once();
        });
    }
}
