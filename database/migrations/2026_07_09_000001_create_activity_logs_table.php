<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action');           // e.g. create, update, delete, login, logout
            $table->string('module');           // e.g. consumable, asset, user, transaction
            $table->string('target_id')->nullable();    // ID of the affected record
            $table->string('target_name')->nullable();  // Human-readable name of the affected record
            $table->json('changes')->nullable();        // Before/after diff
            $table->string('station_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('severity')->default('info'); // info, warning, critical
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
