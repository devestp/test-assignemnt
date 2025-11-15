<?php

namespace App\Providers;

use App\Contracts\Serializers\OrderBookSerializer;
use App\Repositories\OrderBookRepositoryImpl;
use App\Repositories\OrderRepositoryImpl;
use App\Repositories\UserRepositoryImpl;
use App\Serializers\OrderBookSerializerImpl;
use App\Services\AuthServiceImpl;
use Domain\Repositories\OrderBookRepository;
use Domain\Repositories\OrderRepository;
use Domain\Repositories\UserRepository;
use Domain\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthService::class, AuthServiceImpl::class);

        $this->app->bind(OrderRepository::class, OrderRepositoryImpl::class);
        $this->app->bind(OrderBookRepository::class, OrderBookRepositoryImpl::class);
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);

        $this->app->bind(OrderBookSerializer::class, OrderBookSerializerImpl::class);
    }

    public function boot(): void
    {
        //
    }
}
