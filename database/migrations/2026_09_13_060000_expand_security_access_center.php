<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_cards', function (Blueprint $table) {
            $table->string('department')->nullable()->after('user_id');
            $table->json('permitted_areas')->nullable()->after('notes');
            $table->foreignId('replaced_card_id')->nullable()->after('permitted_areas')->constrained('access_cards')->nullOnDelete();
        });

        Schema::create('access_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('area');
            $table->string('device_type');
            $table->string('status')->default('online');
            $table->timestamp('last_communication')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restricted_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('severity')->default('high');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('security_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type');
            $table->string('severity')->default('warning');
            $table->foreignId('access_card_id')->nullable()->constrained('access_cards')->nullOnDelete();
            $table->foreignId('access_log_id')->nullable()->constrained('access_logs')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('status')->default('open');
            $table->text('description')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_alerts');
        Schema::dropIfExists('restricted_areas');
        Schema::dropIfExists('access_points');
        Schema::table('access_cards', function (Blueprint $table) {
            $table->dropForeign(['replaced_card_id']);
            $table->dropColumn(['department', 'permitted_areas', 'replaced_card_id']);
        });
    }
};
