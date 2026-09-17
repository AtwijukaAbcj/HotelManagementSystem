<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pos_orders')
            ->whereNull('reference_number')
            ->orderBy('id')
            ->eachById(function ($order): void {
                DB::table('pos_orders')
                    ->where('id', $order->id)
                    ->update([
                        'reference_number' => 'POS-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('pos_orders')
            ->where('reference_number', 'like', 'POS-%')
            ->update(['reference_number' => null]);
    }
};
