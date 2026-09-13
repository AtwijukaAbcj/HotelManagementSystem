<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('po_number')->nullable()->unique()->after('id');
            $table->date('purchase_date')->nullable()->after('supplier');
            $table->date('expected_delivery_date')->nullable()->after('purchase_date');
            $table->string('invoice_reference')->nullable()->after('expected_delivery_date');
            $table->string('warehouse')->nullable()->after('invoice_reference');
            $table->string('payment_terms')->nullable()->after('warehouse');
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->decimal('subtotal', 12, 2)->default(0)->after('unit_cost');
            $table->decimal('tax', 12, 2)->default(0)->after('subtotal');
            $table->decimal('discount', 12, 2)->default(0)->after('tax');
            $table->decimal('total', 12, 2)->default(0)->after('discount');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->restrictOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('received_quantity', 12, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'po_number', 'purchase_date', 'expected_delivery_date', 'invoice_reference',
                'warehouse', 'payment_terms', 'payment_status', 'subtotal', 'tax', 'discount', 'total',
            ]);
        });
    }
};