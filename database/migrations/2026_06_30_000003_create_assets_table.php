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
        Schema::create('assets', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "a1", "a2"
            $table->string('tag'); // e.g. "VH-1042"
            $table->string('name');
            $table->string('type'); // "Vehicle", "Machinery", "Tool"
            $table->string('station_id');
            $table->string('status'); // "operational", "maintenance", "retired"
            $table->string('assigned_to')->nullable();
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
