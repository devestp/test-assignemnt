<?php

namespace App\Models;

use App\Casts\OrderBookCast;
use Core\Database\Eloquent\Model;
use Database\Factories\OrderBookSnapshotFactory;
use Domain\ValueObjects\OrderBook;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read OrderBook $data
 */
class OrderBookSnapshot extends Model
{
    /** @use HasFactory<OrderBookSnapshotFactory> */
    use HasFactory;

    public const DATA = 'data';

    protected $fillable = [
        self::DATA,
    ];

    protected $casts = [
        self::DATA => OrderBookCast::class,
    ];
}
