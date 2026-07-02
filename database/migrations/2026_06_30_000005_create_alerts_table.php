<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "al1", "al2"
            $table->string('type'); // "low_stock", "pending_arrival", "maintenance"
            $table->text('message');
            $table->string('severity'); // "high", "medium", "low"
            $table->string('at'); // string representing relative time or date, e.g., "2h ago"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
