<?php

use App\Enum\Table;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Table::USERS->value, function (Blueprint $table) {
            $table->id();

            $table->string(User::EMAIL)->unique();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();

            $table->string('token');

            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable();

            $table->text('user_agent')->nullable();

            $table->longText('payload');

            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Table::USERS->value);
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
