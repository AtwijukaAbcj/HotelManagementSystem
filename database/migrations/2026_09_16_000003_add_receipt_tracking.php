<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->string('receipt_email')->nullable()->after('notes');
            $table->string('receipt_email_status')->default('not_sent')->after('receipt_email');
            $table->timestamp('receipt_sent_at')->nullable()->after('receipt_email_status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('guest_id')->nullable()->after('id')->constrained('guests')->nullOnDelete();
            $table->string('receipt_email')->nullable()->after('guest_id');
            $table->string('receipt_email_status')->default('not_sent')->after('receipt_email');
            $table->timestamp('receipt_sent_at')->nullable()->after('receipt_email_status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('guest_id')->nullable()->after('invoice_id')->constrained('guests')->nullOnDelete();
            $table->string('payer_email')->nullable()->after('payer_name');
            $table->string('receipt_email_status')->default('not_sent')->after('notes');
            $table->timestamp('receipt_sent_at')->nullable()->after('receipt_email_status');
        });

        Schema::table('billings', function (Blueprint $table) {
            $table->foreignId('guest_id')->nullable()->after('id')->constrained('guests')->nullOnDelete();
            $table->string('receipt_number')->nullable()->unique()->after('guest_id');
            $table->string('email')->nullable()->after('name');
            $table->string('receipt_email_status')->default('not_sent')->after('email');
            $table->timestamp('receipt_sent_at')->nullable()->after('receipt_email_status');
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropColumn(['guest_id', 'receipt_number', 'email', 'receipt_email_status', 'receipt_sent_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropColumn(['guest_id', 'payer_email', 'receipt_email_status', 'receipt_sent_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropColumn(['guest_id', 'receipt_email', 'receipt_email_status', 'receipt_sent_at']);
        });

        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropColumn(['receipt_email', 'receipt_email_status', 'receipt_sent_at']);
        });
    }
};
