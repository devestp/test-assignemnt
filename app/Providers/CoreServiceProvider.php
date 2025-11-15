<?php

namespace App\Providers;

use Core\Idempotency\Contracts\Idempotency;
use Core\Idempotency\Services\IdempotencyService;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Idempotency::class, IdempotencyService::class);
    }

    public function boot(): void
    {
        //
    }
}
