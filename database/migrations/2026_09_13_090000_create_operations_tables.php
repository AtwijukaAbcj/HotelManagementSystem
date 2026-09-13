<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_id');
            $table->string('task_type', 100);
            $table->string('assignee', 100);
            $table->string('priority', 20)->default('normal');
            $table->string('status', 30)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'priority']);
            $table->foreign('room_id')->references('id')->on('addrooms')->cascadeOnDelete();
        });

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_id');
            $table->string('title', 150);
            $table->text('description');
            $table->string('priority', 20)->default('normal');
            $table->string('status', 30)->default('open');
            $table->string('requested_by', 100)->nullable();
            $table->string('assigned_to', 100)->nullable();
            $table->timestamps();
            $table->index(['status', 'priority']);
            $table->foreign('room_id')->references('id')->on('addrooms')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('housekeeping_tasks');
    }
};
