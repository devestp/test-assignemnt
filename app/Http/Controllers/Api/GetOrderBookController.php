<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderBookResource;
use Domain\Aggregates\OrderBook\GetOrderBook;
use Domain\ValueObjects\OrderBook;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetOrderBookController extends Controller
{
    public function __construct(
        private readonly GetOrderBook $getOrderBook,
    ) {}

    public function __invoke(): JsonResponse
    {
        try {
            return $this->tryGettingOrderBook();
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    private function tryGettingOrderBook(): JsonResponse
    {
        $orderBook = $this->getOrderBook->handle();

        return $this->successResponse($orderBook);
    }

    private function handleException(Exception $e): JsonResponse
    {
        DB::rollBack();

        return $this->handleUnknownException($e);
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

    private function successResponse(OrderBook $orderBook): JsonResponse
    {
        return response()
            ->json(OrderBookResource::make($orderBook));
    }

    private function serverErrorResponse(): JsonResponse
    {
        return response()
            ->json(status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
