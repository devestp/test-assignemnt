<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Database\Factories\OrderFactory;
use Domain\Aggregates\Order\SetOrder;
use Domain\Exceptions\NotEnoughCreditException;
use Domain\Exceptions\UserNotAuthenticatedException;
use Domain\ValueObjects\SetOrderData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class SimulateOrdersCommand extends Command
{
    private const INITIAL = 'initial';

    protected $signature = 'app:simulate-orders {--'.self::INITIAL.'=}';

    protected $description = 'Simulates order requests';

    protected bool $running = true;

    public function handle(): int
    {
        if ($this->hasInitial()) {
            $this->createInitialOrders();
        }

        $this->info('Simulating... Press Ctrl+C to stop.');

        $this->watchForSignals();

        while ($this->running) {
            $this->simulateNewOrder();

            sleep(5);
        }

        $this->info('Exited successfully.');

        return self::SUCCESS;
    }

    private function hasInitial(): bool
    {
        return $this->getInitial() != null;
    }

    private function getInitial(): ?int
    {
        return $this->option(self::INITIAL);
    }

    private function createInitialOrders(): void
    {
        $count = $this->getInitial() ?? throw new RuntimeException('Initial orders not set. Did you forget to check the existence first?');

        for ($i = 0; $i < $count; $i++) {
            $this->factoryOrder()
                ->for($this->getRandomUser())
                ->create();
        }

        $this->info("Created $count initial orders.");
    }

    private function watchForSignals(): void
    {
        if (! function_exists('pcntl_async_signals')) {
            throw new RuntimeException('pcntl_async_signals not available');
        }

        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () {
            $this->running = false;

            $this->info("\nStopping...");
        });
    }

    private function simulateNewOrder(): void
    {
        try {
            $this->trySimulateNewOrder();
        } catch (NotEnoughCreditException) {
            $this->simulateNewOrder();
        }
    }

    /**
     * @throws UserNotAuthenticatedException
     * @throws NotEnoughCreditException
     */
    private function trySimulateNewOrder(): void
    {
        /** @var SetOrder $aggregate */
        $aggregate = resolve(SetOrder::class);

        Auth::setUser($this->getRandomUser());

        $order = $this->factoryOrder()
            ->makeOne();

        $aggregate->handle(
            (new SetOrderData(
                type: $order->type,
                amount: $order->amount,
                price: $order->price,
            ))->additional('idempotencyToken', Str::uuid()),
        );

        $this->info('New order set.');
    }

    private function getRandomUser(): User
    {
        return User::query()
            ->inRandomOrder()
            ->first();
    }

    private function factoryOrder(): OrderFactory
    {
        return Order::factory()
            ->withoutParents()
            ->priceBetween(1000, 1002)
            ->amountBetween(0, 100);
    }
}
