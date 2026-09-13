<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('unit');
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('reorder_level', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('supplier');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('inventory_items');
    }
};
