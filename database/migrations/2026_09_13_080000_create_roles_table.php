<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('roles')->insert(collect(config('module_permissions.roles', []))
            ->map(fn (string $name) => ['name' => $name, 'created_at' => $now, 'updated_at' => $now])
            ->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
