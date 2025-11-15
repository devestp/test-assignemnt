<?php

namespace Tests\Feature\Repositories;

use App\Contracts\Serializers\OrderBookSerializer;
use App\Models\OrderBookSnapshot;
use Domain\Repositories\OrderBookRepository;
use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Domain\ValueObjects\OrderBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderBookRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private int $timesQueryIsCalled = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->timesQueryIsCalled = 0;
    }

    public function test_save_method_saves_the_order_book_in_db_and_cache()
    {
        $orderBook = $this->createOrderBook();
        $repo = $this->createRepo();

        $repo->save($orderBook);

        $this->assertOrderBookSnapshotIsSaved($orderBook);
        $this->assertCachedOrderBookEquals($orderBook);
    }

    public function test_get_method_returns_empty_order_book_if_no_snapshot_exists()
    {
        $repo = $this->createRepo();

        $result = $repo->get();

        $this->assertOrderBookIsEmpty($result);
        $this->assertCachedOrderBookIsEmpty();
    }

    public function test_get_method_retrieves_the_latest_order_book_from_db_and_caches_it()
    {
        $this->createOldOrderBookSnapshots();
        $orderBookSnapshot = $this->createOrderBookSnapshot();
        $repo = $this->createRepo();

        $result = $repo->get();

        $this->assertOrderBookSnapshotEquals($orderBookSnapshot, $result);
        $this->assertCachedOrderBookEqualsSnapshot($orderBookSnapshot);
    }

    public function test_get_method_retrieves_the_order_book_from_cache_if_is_cached()
    {
        $this->listenToOrderBookDbQueries();
        $orderBookSnapshot = $this->createOrderBookSnapshot();
        $repo = $this->createRepo();
        // Retrieves and caches the order book
        $repo->get();

        $result = $repo->get();

        $this->assertOrderBookSnapshotEquals($orderBookSnapshot, $result);
        $this->assertCachedOrderBookEqualsSnapshot($orderBookSnapshot);
        $this->assertOrderBookIsQueriedOnce();
    }

    private function createOrderBook(): OrderBook
    {
        return new OrderBook(
            groupedBuyOrders: new GroupedOrders(
                collect([
                    new GroupedOrder(
                        price: 10.5,
                        count: 2,
                    ),
                    new GroupedOrder(
                        price: 10,
                        count: 15,
                    ),
                    new GroupedOrder(
                        price: 9.2,
                        count: 1,
                    ),
                ])
            ),
            groupedSellOrders: new GroupedOrders(
                collect([
                    new GroupedOrder(
                        price : 9.1,
                        count : 5,
                    ),
                    new GroupedOrder(
                        price : 9.8,
                        count : 5,
                    ),
                    new GroupedOrder(
                        price : 10,
                        count : 15,
                    ),
                ])
            ),
        );
    }

    private function createOldOrderBookSnapshots(): void
    {
        OrderBookSnapshot::factory()
            ->sequence(
                ['created_at' => now()->subMinute()],
                ['created_at' => now()->subMinutes(2)],
            )->count(2)
            ->create();
    }

    private function createOrderBookSnapshot(): OrderBookSnapshot
    {
        return OrderBookSnapshot::factory()
            ->create();
    }

    private function listenToOrderBookDbQueries(): void
    {
        DB::listen(function ($query) {
            // Filter only select queries
            if (Str::contains($query->sql, 'select')) {
                $this->timesQueryIsCalled++;
            }
        });
    }

    private function createRepo(): OrderBookRepository
    {
        return resolve(OrderBookRepository::class);
    }

    private function assertOrderBookIsEmpty(OrderBook $orderBook): void
    {
        $this->assertEmpty(
            $orderBook->getGroupedSellOrders()->getGroups(),
        );
        $this->assertEmpty(
            $orderBook->getGroupedBuyOrders()->getGroups(),
        );
    }

    private function assertOrderBookSnapshotEquals(
        OrderBookSnapshot $orderBookSnapshot,
        OrderBook $orderBook,
    ): void {
        $this->assertEquals($orderBookSnapshot->data, $orderBook);
    }

    private function assertOrderBookSnapshotIsSaved(OrderBook $orderBook): void
    {
        $this->assertDatabaseCount(OrderBookSnapshot::class, 1);

        $orderBookSnapshot = OrderBookSnapshot::query()->sole();

        $this->assertOrderBookSnapshotEquals($orderBookSnapshot, $orderBook);
    }

    private function assertCachedOrderBookIsEmpty(): void
    {
        $this->assertOrderBookIsEmpty(
            $this->getCachedOrderBook(),
        );
    }

    private function assertCachedOrderBookEquals(OrderBook $orderBook): void
    {
        $this->assertEquals($orderBook, $this->getCachedOrderBook());
    }

    private function assertCachedOrderBookEqualsSnapshot(OrderBookSnapshot $orderBookSnapshot): void
    {
        $this->assertEquals($orderBookSnapshot->data, $this->getCachedOrderBook());
    }

    private function getCachedOrderBook(): OrderBook
    {
        $orderBook = Cache::get('order_book');

        return resolve(OrderBookSerializer::class)
            ->deserialize($orderBook);
    }

    private function assertOrderBookIsQueriedOnce(): void
    {
        $this->assertEquals(
            1,
            $this->timesQueryIsCalled,
            'Failed asserting that query is called once',
        );
    }
}
