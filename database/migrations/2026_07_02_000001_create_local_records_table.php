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
        Schema::create('local_records', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type'); // "assign_asset", "issue_stock", "borrow_stock"
            $table->string('station_id');
            $table->string('worker_name');
            $table->string('item_id');
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('pcs');
            $table->string('status')->default('active'); // "active", "returned"
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_records');
    }
};
