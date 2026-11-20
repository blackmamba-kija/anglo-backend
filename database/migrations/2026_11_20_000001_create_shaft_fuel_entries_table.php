<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shaft_fuel_entries', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('station_id');
            $table->string('shaft_name')->default('Main Shaft');
            $table->enum('kind', ['received', 'used']);
            $table->decimal('quantity', 14, 2);
            $table->string('unit')->default('L');
            $table->string('item_name')->default('Diesel Fuel');
            $table->string('source_consumable_id')->nullable();
            $table->date('date');
            $table->string('recorded_by');
            $table->string('issued_to')->nullable();
            $table->string('purpose')->nullable();
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('stations')->cascadeOnDelete();
            $table->index(['station_id', 'shaft_name', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shaft_fuel_entries');
    }
};
