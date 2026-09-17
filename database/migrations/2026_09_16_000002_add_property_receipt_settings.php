<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'phone')) {
                $table->string('phone')->nullable()->after('timezone');
            }

            if (!Schema::hasColumn('properties', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('properties', 'receipt_name')) {
                $table->string('receipt_name')->nullable()->after('email');
            }

            if (!Schema::hasColumn('properties', 'receipt_header')) {
                $table->string('receipt_header')->nullable()->after('receipt_name');
            }

            if (!Schema::hasColumn('properties', 'receipt_footer')) {
                $table->text('receipt_footer')->nullable()->after('receipt_header');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $columns = ['phone', 'email', 'receipt_name', 'receipt_header', 'receipt_footer'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('properties', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
