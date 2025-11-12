<?php

use App\Enum\Table;
use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Table::ORDERS->value, function (Blueprint $table) {
            $table->id();

            $table->foreignId(Order::USER_ID)
                ->references('id')
                ->on(Table::USERS->value)
                ->restrictOnDelete();

            $table->float(Order::AMOUNT, 6);

            $table->float(Order::PRICE, 6);

            // Used raw enum raw values to make sure future changes
            // to the enum, won't break this migration.
            $table->enum(Order::TYPE, ['buy', 'sell']);

            $table->string(Order::IDEMPOTENCY_TOKEN);

            // Used raw enum raw values to make sure future changes
            // to the enum, won't break this migration.
            $table->enum(Order::STATE, ['pending', 'completed']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Table::ORDERS->value);
    }
};
