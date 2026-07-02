<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('station_id');
            $table->integer('quantity');
            $table->string('unit');
            $table->integer('reorder_level');
            $table->enum('status', ['pending', 'accepted', 'approved', 'rejected'])->default('pending');
            $table->string('requested_by'); // user id of procurement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_requests');
    }
};
?>
