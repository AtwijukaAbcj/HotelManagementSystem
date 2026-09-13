<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->string('card_type');
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['card_type', 'status']);
        });

        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_card_id')->nullable()->constrained('access_cards')->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('guests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('addrooms')->nullOnDelete();
            $table->string('access_point');
            $table->string('area');
            $table->string('event_type')->default('entry');
            $table->string('result')->default('granted');
            $table->string('reason')->nullable();
            $table->timestamp('accessed_at');
            $table->timestamps();
            $table->index(['accessed_at', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('access_cards');
    }
};
