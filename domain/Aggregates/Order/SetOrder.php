<?php

namespace Domain\Aggregates\Order;

use Domain\Entities\Order;
use Domain\Entities\User;
use Domain\Exceptions\NotEnoughCreditException;
use Domain\Exceptions\UserNotAuthenticatedException;
use Domain\Repositories\OrderRepository;
use Domain\Repositories\UserRepository;
use Domain\Services\AuthService;
use Domain\ValueObjects\CreateOrderData;
use Domain\ValueObjects\SetOrderData;
use Illuminate\Support\HigherOrderTapProxy;

/**
 * Sets a new order in the system.
 *
 * This aggregate is safe to be used in transactions.
 */
class SetOrder
{
    public function __construct(
        private AuthService $authService,
        private OrderRepository $orderRepository,
        private UserRepository $userRepository,
    ) {}

    /**
     * @throws UserNotAuthenticatedException
     * @throws NotEnoughCreditException
     */
    public function handle(SetOrderData $data): void
    {
        $user = $this->getCurrentUser();

        if (! $this->doesUserHaveEnoughCredit($user, $data)) {
            throw new NotEnoughCreditException($user);
        }

        $order = $this->createOrder($user, $data);

        $this->subtractFromUserCredit($user, $data);

        $this->matchWithOppositeOrder($order);
    }

    /**
     * @throws UserNotAuthenticatedException
     */
    private function getCurrentUser(): User|HigherOrderTapProxy|null
    {
        return tap(
            $this->authService->currentUser(),
            // Negative space check
            fn (?User $user) => $user ?? throw new UserNotAuthenticatedException,
        );
    }

    private function doesUserHaveEnoughCredit(User $user, SetOrderData $data): bool
    {
        return $user->hasCredit(
            $data->getAmount(),
        );
    }

    private function createOrder(User $user, SetOrderData $setOrderData): Order
    {
        $createOrderData = new CreateOrderData(
            userId: $user->getId(),
            type: $setOrderData->getType(),
            amount: $setOrderData->getAmount(),
            price: $setOrderData->getPrice(),
        );

        // Because we know that the passed VO supports additional
        // bag, we simply pass the additional data to the next VO
        $createOrderData->additional($setOrderData->getAdditional());

        return $this->orderRepository->create($createOrderData);
    }

    private function subtractFromUserCredit(User $user, SetOrderData $data): void
    {
        $price = $data->getPrice();
        $amount = $data->getAmount();

        $user->subtractCredit(
            $amount->multipliedBy($price),
        );

        $this->userRepository->saveCredit($user);
    }

    private function matchWithOppositeOrder(Order $order): void
    {
        $matchedOrder = $this->orderRepository->getOldestMatchingOrderTo($order);

        if (! is_null($matchedOrder)) {
            $this->orderRepository->matchOrders(
                first: $order,
                second: $matchedOrder,
            );
        }
    }
}
