<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('billings')
            ->whereNull('receipt_number')
            ->orderBy('id')
            ->eachById(function ($billing): void {
                DB::table('billings')
                    ->where('id', $billing->id)
                    ->update(['receipt_number' => 'BILL-' . str_pad((string) $billing->id, 6, '0', STR_PAD_LEFT)]);
            });
    }

    public function down(): void
    {
        DB::table('billings')->update(['receipt_number' => null]);
    }
};
