<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SetOrderRequest;
use App\Http\Resources\Api\ReasonableResource;
use App\Models\Order;
use Core\Idempotency\Contracts\Idempotency;
use Domain\Aggregates\Order\SetOrder;
use Domain\Exceptions\NotEnoughCreditException;
use Domain\Exceptions\UserNotAuthenticatedException;
use Domain\ValueObjects\SetOrderData;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetOrderController extends Controller
{
    public function __construct(
        private readonly Idempotency $idempotency,
        private readonly SetOrder $setOrder,
    ) {}

    public function __invoke(SetOrderRequest $request): JsonResponse
    {
        try {
            return $this->trySettingOrder($request);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @throws UserNotAuthenticatedException
     * @throws NotEnoughCreditException
     */
    private function trySettingOrder(SetOrderRequest $request): JsonResponse
    {
        if ($this->isIdempotent($request)) {
            return $this->successResponse();
        }

        $this->doSetOrder($request);

        return $this->successResponse();
    }

    private function isIdempotent(SetOrderRequest $request): bool
    {
        return $this->idempotency->check(
            provider: Order::class,
            token: $request->getIdempotencyToken(),
        );
    }

    /**
     * @throws UserNotAuthenticatedException
     * @throws NotEnoughCreditException
     */
    private function doSetOrder(SetOrderRequest $request): void
    {
        $data = new SetOrderData(
            type: $request->getType(),
            amount: $request->getAmount(),
            price: $request->getPrice(),
        );

        $data->additional('idempotencyToken', $request->getIdempotencyToken());

        DB::beginTransaction();

        $this->setOrder->handle($data);

        DB::commit();
    }

    private function handleException(Exception $e): JsonResponse
    {
        DB::rollBack();

        return match (true) {
            $e instanceof NotEnoughCreditException => $this->handleNotEnoughCreditException($e),
            $e instanceof UserNotAuthenticatedException => $this->handleUserNotAuthenticatedException(),
            default => $this->handleUnknownException($e),
        };
    }

    private function handleNotEnoughCreditException(NotEnoughCreditException $e): JsonResponse
    {
        Log::info(
            message: 'Request sent by a user that does not have enough credit.',
            context: [
                'user_id' => $e->user->getId(),
            ],
        );

        return $this->notEnoughCreditResponse();
    }

    private function handleUserNotAuthenticatedException(): JsonResponse
    {
        Log::error(
            message: 'Request sent to an authenticated endpoint but the user is not authenticated. '.
            'Did you forget to set the auth middleware on the route?',
        );

        return $this->serverErrorResponse();
    }

    private function handleUnknownException(Exception $e): JsonResponse
    {
        Log::critical(
            message: 'Unknown exception happened: '.$e->getMessage(),
            context: [
                'exception' => $e,
            ],
        );

        return $this->serverErrorResponse();
    }

    private function successResponse(): JsonResponse
    {
        return response()
            ->json(status: Response::HTTP_CREATED);
    }

    private function notEnoughCreditResponse(): JsonResponse
    {
        return response()
            ->json(
                data: ReasonableResource::make('notEnoughCredit'),
                status: Response::HTTP_FORBIDDEN,
            );
    }

    private function serverErrorResponse(): JsonResponse
    {
        return response()
            ->json(status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
