<?php

namespace Tests\Feature;

use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShaftDailyReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_save_and_submit_a_complete_daily_report(): void
    {
        Station::create(['id' => 'st-1', 'name' => 'Station 1', 'location' => 'Mine', 'manager' => 'Manager', 'status' => 'active']);
        $payload = [
            'station_id' => 'st-1', 'shaft_name' => 'Main Shaft', 'report_date' => '2026-11-20',
            'shift' => 'Day', 'supervisor_name' => 'Supervisor', 'status' => 'draft',
            'production_tonnes' => 420, 'advance_meters' => 3.5, 'workforce_count' => 18,
            'downtime_hours' => 1.25, 'production_summary' => 'Target achieved',
            'db_summary' => 'D&B issued', 'fuel_summary' => 'Closing 900 L',
            'delivery_summary' => 'Pump spares received', 'safety_summary' => 'No incidents',
            'equipment_summary' => 'One loader on maintenance', 'challenges' => 'Water ingress',
            'actions_taken' => 'Pump installed', 'next_shift_plan' => 'Continue dewatering',
            'additional_notes' => 'Handover completed', 'source_snapshot' => ['fuel_entries' => []],
        ];

        $created = $this->postJson('/api/shaft-daily-reports', $payload)
            ->assertCreated()->assertJsonPath('status', 'draft')->json();

        $payload['status'] = 'pending_operator';
        $this->putJson('/api/shaft-daily-reports/' . $created['id'], $payload)
            ->assertOk()->assertJsonPath('status', 'pending_operator');

        $this->getJson('/api/shaft-daily-reports?station_id=st-1')
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.production_tonnes', 420);
    }
}
