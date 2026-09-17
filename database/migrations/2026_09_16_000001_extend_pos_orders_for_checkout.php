<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_orders', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('pos_orders', 'property_id')) {
                $table->foreignId('property_id')->nullable()->after('reference_number')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('pos_orders', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->after('property_id')->constrained('guests')->nullOnDelete();
            }

            if (!Schema::hasColumn('pos_orders', 'room_id')) {
                $table->foreignId('room_id')->nullable()->after('guest_id')->constrained('addrooms')->nullOnDelete();
            }

            if (!Schema::hasColumn('pos_orders', 'stay_id')) {
                $table->foreignId('stay_id')->nullable()->after('room_id')->constrained('stays')->nullOnDelete();
            }

            if (!Schema::hasColumn('pos_orders', 'table_reference')) {
                $table->string('table_reference')->nullable()->after('stay_id');
            }

            if (!Schema::hasColumn('pos_orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('table_reference');
            }

            if (!Schema::hasColumn('pos_orders', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            }

            if (!Schema::hasColumn('pos_orders', 'tax')) {
                $table->decimal('tax', 12, 2)->default(0)->after('discount');
            }

            if (!Schema::hasColumn('pos_orders', 'service_charge')) {
                $table->decimal('service_charge', 12, 2)->default(0)->after('tax');
            }

            if (!Schema::hasColumn('pos_orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('service_charge');
            }

            if (!Schema::hasColumn('pos_orders', 'amount_received')) {
                $table->decimal('amount_received', 12, 2)->default(0)->after('payment_method');
            }

            if (!Schema::hasColumn('pos_orders', 'change_due')) {
                $table->decimal('change_due', 12, 2)->default(0)->after('amount_received');
            }

            if (!Schema::hasColumn('pos_orders', 'notes')) {
                $table->text('notes')->nullable()->after('change_due');
            }

            if (!Schema::hasColumn('pos_orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('notes');
            }
        });

        Schema::table('pos_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_order_items', 'product_name_snapshot')) {
                $table->string('product_name_snapshot')->nullable()->after('inventory_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pos_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('pos_order_items', 'product_name_snapshot')) {
                $table->dropColumn('product_name_snapshot');
            }
        });

        Schema::table('pos_orders', function (Blueprint $table) {
            $columns = [
                'reference_number',
                'property_id',
                'guest_id',
                'room_id',
                'stay_id',
                'table_reference',
                'subtotal',
                'discount',
                'tax',
                'service_charge',
                'payment_method',
                'amount_received',
                'change_due',
                'notes',
                'completed_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('pos_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
