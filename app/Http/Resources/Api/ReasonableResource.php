<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read string $resource
 */
class ReasonableResource extends JsonResource
{
    private const REASON = 'reason';

    public function toArray(Request $request): array
    {
        return [
            self::REASON => $this->resource,
        ];
    }
}
