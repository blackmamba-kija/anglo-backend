<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumable_items', function (Blueprint $table) {
            $table->string('fuel_type')->nullable()->after('category');
        });

        DB::table('consumable_items')
            ->where(function ($query) {
                $query->whereRaw('LOWER(category) LIKE ?', ['%diesel%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%diesel%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%diezel%']);
            })
            ->update(['category' => 'Fuel', 'fuel_type' => 'Diesel']);

        DB::table('consumable_items')
            ->where(function ($query) {
                $query->whereRaw('LOWER(category) LIKE ?', ['%petrol%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%petrol%']);
            })
            ->update(['category' => 'Fuel', 'fuel_type' => 'Petrol']);
    }

    public function down(): void
    {
        Schema::table('consumable_items', function (Blueprint $table) {
            $table->dropColumn('fuel_type');
        });
    }
};
