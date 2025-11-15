<?php

use App\Enum\Table;
use App\Models\OrderBookSnapshot;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Table::ORDER_BOOK_SNAPSHOTS->value, function (Blueprint $table) {
            $table->id();

            $table->json(OrderBookSnapshot::DATA);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Table::ORDER_BOOK_SNAPSHOTS->value);
    }
};
