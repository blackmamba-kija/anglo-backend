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
        Schema::create('consumable_items', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "c1", "c2"
            $table->string('name');
            $table->string('category'); // "Fuel", "Oil", "Water", etc.
            $table->string('unit'); // "L", "pcs", "m³", etc.
            $table->string('station_id');
            $table->integer('quantity');
            $table->integer('reorder_level');
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumable_items');
    }
};
