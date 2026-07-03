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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->after('assigned_to');
            $table->string('model')->nullable()->after('serial_number');
            $table->date('purchase_date')->nullable()->after('model');
            $table->decimal('purchase_cost', 15, 2)->nullable()->after('purchase_date');
            $table->text('description')->nullable()->after('purchase_cost');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'serial_number',
                'model',
                'purchase_date',
                'purchase_cost',
                'description',
            ]);
        });
    }
};
