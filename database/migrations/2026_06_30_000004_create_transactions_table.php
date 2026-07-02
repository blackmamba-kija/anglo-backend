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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. "t1", "t2"
            $table->date('date');
            $table->string('item_id');
            $table->string('item_name');
            $table->string('from_location');
            $table->string('to_station_id');
            $table->integer('quantity');
            $table->string('unit');
            $table->string('status'); // "pending", "received", "rejected"
            $table->string('initiated_by');
            $table->timestamps();

            $table->foreign('to_station_id')->references('id')->on('stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
