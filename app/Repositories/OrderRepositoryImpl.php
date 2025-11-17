<?php

namespace App\Repositories;

use App\Models\Order as OrderModel;
use Domain\Entities\Order;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Domain\Repositories\OrderRepository;
use Domain\ValueObjects\CreateOrderData;
use Domain\ValueObjects\GroupedOrder;
use Domain\ValueObjects\GroupedOrders;
use Illuminate\Support\Facades\DB;

class OrderRepositoryImpl implements OrderRepository
{
    public function createForUpdate(CreateOrderData $data): Order
    {
        $createdOrder = OrderModel::create([
            OrderModel::USER_ID => $data->getUserId(),
            OrderModel::AMOUNT => $data->getAmount(),
            OrderModel::PRICE => $data->getPrice(),
            OrderModel::TYPE => $data->getType(),
            OrderModel::IDEMPOTENCY_TOKEN => $data->getAdditionalOrFail('idempotencyToken'),
            OrderModel::STATE => OrderState::PENDING,
        ]);

        // Another select is needed to ensure the record is locked
        $lockedOrder = OrderModel::query()
            ->lockForUpdate()
            ->whereKey($createdOrder->getKey())
            ->first();

        return $lockedOrder->toEntity();
    }

    public function getPendingBuysForOrderBook(): GroupedOrders
    {
        return $this->getPendingForOrderBook(OrderType::BUY);
    }

    public function getPendingSellsForOrderBook(): GroupedOrders
    {
        return $this->getPendingForOrderBook(OrderType::SELL);
    }

    public function getOldestMatchingOrderForUpdate(Order $order): ?Order
    {
        return OrderModel::query()
            ->lockForUpdate()
            ->where(OrderModel::AMOUNT, $order->getAmount())
            ->where(OrderModel::PRICE, $order->getPrice())
            ->where(OrderModel::TYPE, $order->getType()->opposite())
            ->where(OrderModel::STATE, OrderState::PENDING)
            ->whereKeyNot($order->getId())
            ->oldest()
            ->first()
            ?->toEntity();
    }

    public function matchOrders(Order $first, Order $second): void
    {
        $this->matchOrderWith($first, $second);

        $this->matchOrderWith($second, $first);
    }

    private function getPendingForOrderBook(OrderType $type): GroupedOrders
    {
        $totalColumnName = 'total';

        $result = OrderModel::query()
            ->groupBy(OrderModel::PRICE)
            ->where(OrderModel::TYPE, $type)
            ->where(OrderModel::STATE, OrderState::PENDING)
            ->select(OrderModel::PRICE, DB::raw("COUNT(*) as $totalColumnName"))
            ->orderBy(OrderModel::PRICE, $type == OrderType::BUY ? 'DESC' : 'ASC')
            ->get()
            ->map(fn (OrderModel $order) => new GroupedOrder(price: $order->price, count: $order->{$totalColumnName}));

        return new GroupedOrders($result);
    }

    private function matchOrderWith(Order $first, Order $second): void
    {
        OrderModel::query()
            ->whereKey($first->getId())
            ->update([
                OrderModel::STATE => OrderState::COMPLETED,
                OrderModel::MATCHED_ORDER_ID => $second->getId(),
            ]);
    }
}
