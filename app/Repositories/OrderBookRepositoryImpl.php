<?php

namespace App\Repositories;

use App\Contracts\Serializers\OrderBookSerializer;
use App\Models\OrderBookSnapshot;
use Domain\Repositories\OrderBookRepository;
use Domain\ValueObjects\OrderBook;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class OrderBookRepositoryImpl implements OrderBookRepository
{
    private const ORDER_BOOK_CACHE_KEY = 'order_book';

    public function __construct(
        private readonly OrderBookSerializer $orderBookSerializer,
    ) {}

    public function save(OrderBook $orderBook): void
    {
        // Persist the order book to the database
        // if the cache is unavailable
        $this->saveInDb($orderBook);

        // Cache the order book for faster reads
        $this->saveInCache($orderBook);
    }

    public function get(): OrderBook
    {
        if ($this->isOrderBookCached()) {
            return $this->getFromCache();
        }

        $orderBook = $this->getFromDb();

        $this->saveInCache($orderBook);

        return $orderBook;
    }

    private function saveInDb(OrderBook $orderBook): void
    {
        OrderBookSnapshot::create([
            OrderBookSnapshot::DATA => $orderBook,
        ]);
    }

    private function saveInCache(OrderBook $orderBook): void
    {
        Cache::set(
            key: self::ORDER_BOOK_CACHE_KEY,
            value: $this->orderBookSerializer->serialize($orderBook),
            ttl: 60,
        );
    }

    private function isOrderBookCached(): bool
    {
        return Cache::has(self::ORDER_BOOK_CACHE_KEY);
    }

    private function getFromCache(): OrderBook
    {
        // Negative space check
        if (! $this->isOrderBookCached()) {
            throw new RuntimeException(
                'The order book is not cached but we tried to fetch '.
                'it from the cache. Did you forget to first check its existence?'
            );
        }

        return $this->orderBookSerializer->deserialize(
            Cache::get(self::ORDER_BOOK_CACHE_KEY),
        );
    }

    private function getFromDb(): OrderBook
    {
        $snapshot = OrderBookSnapshot::query()
            ->latest()
            ->first();

        // In scenarios where the application is fresh or the data
        // is cleaned up, no snapshot may be available, therefore
        // we check & return empty order book for this use case.
        if (is_null($snapshot)) {
            return OrderBook::empty();
        }

        return $snapshot->data;
    }
}
