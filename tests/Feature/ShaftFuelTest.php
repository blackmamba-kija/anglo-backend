<?php

namespace Tests\Feature;

use App\Models\ConsumableItem;
use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShaftFuelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Station::create(['id' => 'hq', 'name' => 'HQ', 'location' => 'HQ', 'manager' => 'Admin', 'status' => 'active']);
        Station::create(['id' => 'st-1', 'name' => 'Station 1', 'location' => 'Mine', 'manager' => 'Manager', 'status' => 'active']);
        ConsumableItem::create([
            'id' => 'fuel-1', 'name' => 'Diesel Fuel', 'category' => 'Fuel', 'unit' => 'L',
            'station_id' => 'hq', 'quantity' => 1000, 'reorder_level' => 100,
        ]);
    }

    public function test_fuel_is_dispatched_from_hq_and_used_at_the_shaft(): void
    {
        $this->postJson('/api/shaft-fuel', [
            'station_id' => 'st-1', 'shaft_name' => 'Main Shaft', 'kind' => 'received',
            'quantity' => 300, 'date' => '2026-11-20', 'recorded_by' => 'Admin',
            'source_consumable_id' => 'fuel-1',
        ])->assertCreated()->assertJsonPath('quantity', 300);

        $this->assertDatabaseHas('consumable_items', ['id' => 'fuel-1', 'quantity' => 700]);

        $this->postJson('/api/shaft-fuel', [
            'station_id' => 'st-1', 'shaft_name' => 'Main Shaft', 'kind' => 'used',
            'quantity' => 75, 'date' => '2026-11-20', 'recorded_by' => 'Operator',
            'issued_to' => 'Generator Team',
            'purpose' => 'Generator',
        ])->assertCreated();

        $this->getJson('/api/shaft-fuel?station_id=st-1')->assertOk()->assertJsonCount(2);
    }

    public function test_usage_cannot_exceed_shaft_balance(): void
    {
        $this->postJson('/api/shaft-fuel', [
            'station_id' => 'st-1', 'shaft_name' => 'Main Shaft', 'kind' => 'used',
            'quantity' => 1, 'date' => '2026-11-20', 'recorded_by' => 'Operator',
            'issued_to' => 'Generator Team',
        ])->assertUnprocessable()->assertJsonValidationErrors('quantity');
    }
}
