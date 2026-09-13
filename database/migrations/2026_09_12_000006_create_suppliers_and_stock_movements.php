<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('supplier')->constrained('suppliers')->nullOnDelete();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('supplier')->constrained('suppliers')->nullOnDelete();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('movement_type');
            $table->decimal('quantity', 12, 2);
            $table->decimal('stock_before', 12, 2)->default(0);
            $table->decimal('stock_after', 12, 2)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::table('purchase_orders', function (Blueprint $table) { $table->dropForeign(['supplier_id']); $table->dropColumn('supplier_id'); });
        Schema::table('inventory_items', function (Blueprint $table) { $table->dropForeign(['supplier_id']); $table->dropColumn('supplier_id'); });
        Schema::dropIfExists('suppliers');
    }
};