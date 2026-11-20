<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shaft_daily_reports', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('station_id');
            $table->string('shaft_name')->default('Main Shaft');
            $table->date('report_date');
            $table->string('shift');
            $table->string('supervisor_name');
            $table->string('status')->default('draft');
            $table->decimal('production_tonnes', 14, 2)->default(0);
            $table->decimal('advance_meters', 14, 2)->default(0);
            $table->unsignedInteger('workforce_count')->default(0);
            $table->decimal('downtime_hours', 8, 2)->default(0);
            $table->text('production_summary')->nullable();
            $table->text('db_summary')->nullable();
            $table->text('fuel_summary')->nullable();
            $table->text('delivery_summary')->nullable();
            $table->text('safety_summary')->nullable();
            $table->text('equipment_summary')->nullable();
            $table->text('challenges')->nullable();
            $table->text('actions_taken')->nullable();
            $table->text('next_shift_plan')->nullable();
            $table->text('additional_notes')->nullable();
            $table->json('source_snapshot')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('stations')->cascadeOnDelete();
            $table->unique(['station_id', 'shaft_name', 'report_date', 'shift'], 'shaft_report_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shaft_daily_reports');
    }
};
