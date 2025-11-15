<?php

use App\Enum\Table;
use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Table::ADMINS->value, function (Blueprint $table) {
            $table->id();

            $table->string(Admin::EMAIL)
                ->unique();

            $table->string(Admin::PASSWORD);

            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Table::ADMINS->value);
    }
};
