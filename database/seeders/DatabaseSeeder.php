<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Station;
use App\Models\ConsumableItem;
use App\Models\Asset;
use App\Models\Transaction;
use App\Models\Alert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Stations
        $stations = [
            ['id' => 'hq', 'name' => 'Headquarters', 'location' => 'Lusaka, ZM', 'manager' => 'Central Ops', 'status' => 'active'],
            ['id' => 'st-01', 'name' => 'Kitwe Pit A', 'location' => 'Kitwe', 'manager' => 'J. Mwale', 'status' => 'active'],
            ['id' => 'st-02', 'name' => 'Ndola Refinery', 'location' => 'Ndola', 'manager' => 'P. Banda', 'status' => 'active'],
            ['id' => 'st-03', 'name' => 'Solwezi Site', 'location' => 'Solwezi', 'manager' => 'R. Phiri', 'status' => 'maintenance'],
            ['id' => 'st-04', 'name' => 'Chingola Crusher', 'location' => 'Chingola', 'manager' => 'M. Zulu', 'status' => 'active'],
        ];

        foreach ($stations as $s) {
            Station::updateOrCreate(['id' => $s['id']], $s);
        }

        // Seed Consumables
        $consumables = [
            ['id' => 'c1', 'name' => 'Diesel Fuel', 'category' => 'Fuel', 'unit' => 'L', 'station_id' => 'st-01', 'quantity' => 4200, 'reorder_level' => 5000],
            ['id' => 'c2', 'name' => 'Engine Oil 15W-40', 'category' => 'Oil', 'unit' => 'L', 'station_id' => 'st-01', 'quantity' => 320, 'reorder_level' => 200],
            ['id' => 'c3', 'name' => 'Hydraulic Oil', 'category' => 'Oil', 'unit' => 'L', 'station_id' => 'st-02', 'quantity' => 180, 'reorder_level' => 250],
            ['id' => 'c4', 'name' => 'Industrial Water', 'category' => 'Water', 'unit' => 'm³', 'station_id' => 'st-02', 'quantity' => 920, 'reorder_level' => 500],
            ['id' => 'c5', 'name' => 'Grease Cartridges', 'category' => 'Lubricant', 'unit' => 'pcs', 'station_id' => 'st-03', 'quantity' => 48, 'reorder_level' => 60],
            ['id' => 'c6', 'name' => 'Diesel Fuel', 'category' => 'Fuel', 'unit' => 'L', 'station_id' => 'st-03', 'quantity' => 7800, 'reorder_level' => 5000],
            ['id' => 'c7', 'name' => 'Flotation Reagent', 'category' => 'Reagent', 'unit' => 'kg', 'station_id' => 'st-04', 'quantity' => 140, 'reorder_level' => 200],
            ['id' => 'c8', 'name' => 'Coolant', 'category' => 'Consumable', 'unit' => 'L', 'station_id' => 'st-04', 'quantity' => 410, 'reorder_level' => 300],
            // HQ Stock (Available for distribution)
            ['id' => 'c_hq1', 'name' => 'HQ Diesel Reserve', 'category' => 'Fuel', 'unit' => 'L', 'station_id' => 'hq', 'quantity' => 50000, 'reorder_level' => 10000],
            ['id' => 'c_hq2', 'name' => 'HQ Engine Oil', 'category' => 'Oil', 'unit' => 'L', 'station_id' => 'hq', 'quantity' => 5000, 'reorder_level' => 1000],
        ];

        foreach ($consumables as $c) {
            ConsumableItem::updateOrCreate(['id' => $c['id']], $c);
        }

        // Seed Assets
        $assets = [
            ['id' => 'a1', 'tag' => 'VH-1042', 'name' => 'Caterpillar 777G Haul Truck', 'type' => 'Vehicle', 'station_id' => 'st-01', 'status' => 'operational', 'assigned_to' => 'Crew A'],
            ['id' => 'a2', 'tag' => 'VH-1043', 'name' => 'Komatsu HD785 Haul Truck', 'type' => 'Vehicle', 'station_id' => 'st-01', 'status' => 'maintenance', 'assigned_to' => null],
            ['id' => 'a3', 'tag' => 'MC-2210', 'name' => 'Liebherr R9400 Excavator', 'type' => 'Machinery', 'station_id' => 'st-02', 'status' => 'operational', 'assigned_to' => 'Crew B'],
            ['id' => 'a4', 'tag' => 'MC-2211', 'name' => 'Atlas Copco DM45 Drill', 'type' => 'Machinery', 'station_id' => 'st-03', 'status' => 'operational', 'assigned_to' => null],
            ['id' => 'a5', 'tag' => 'TL-3301', 'name' => 'Torque Wrench Set', 'type' => 'Tool', 'station_id' => 'st-02', 'status' => 'operational', 'assigned_to' => null],
            ['id' => 'a6', 'tag' => 'VH-1044', 'name' => 'Toyota Land Cruiser Pickup', 'type' => 'Vehicle', 'station_id' => 'st-04', 'status' => 'operational', 'assigned_to' => 'Site Lead'],
            ['id' => 'a7', 'tag' => 'MC-2212', 'name' => 'Sandvik LH517i Loader', 'type' => 'Machinery', 'station_id' => 'st-04', 'status' => 'maintenance', 'assigned_to' => null],
            // HQ Assets (Available for distribution)
            ['id' => 'a_hq1', 'tag' => 'VH-HQ01', 'name' => 'HQ Service Truck', 'type' => 'Vehicle', 'station_id' => 'hq', 'status' => 'operational', 'assigned_to' => null],
            ['id' => 'a_hq2', 'tag' => 'MC-HQ02', 'name' => 'HQ Backup Generator', 'type' => 'Machinery', 'station_id' => 'hq', 'status' => 'operational', 'assigned_to' => null],
        ];

        foreach ($assets as $a) {
            Asset::updateOrCreate(['id' => $a['id']], $a);
        }

        // Seed Transactions
        $transactions = [
            ['id' => 't1', 'date' => '2026-06-28', 'item_id' => 'c1', 'item_name' => 'Diesel Fuel', 'from_location' => 'HQ', 'to_station_id' => 'st-01', 'quantity' => 8000, 'unit' => 'L', 'status' => 'pending', 'initiated_by' => 'Procurement'],
            ['id' => 't2', 'date' => '2026-06-27', 'item_id' => 'c5', 'item_name' => 'Grease Cartridges', 'from_location' => 'HQ', 'to_station_id' => 'st-03', 'quantity' => 120, 'unit' => 'pcs', 'status' => 'pending', 'initiated_by' => 'Procurement'],
            ['id' => 't3', 'date' => '2026-06-26', 'item_id' => 'c3', 'item_name' => 'Hydraulic Oil', 'from_location' => 'HQ', 'to_station_id' => 'st-02', 'quantity' => 400, 'unit' => 'L', 'status' => 'received', 'initiated_by' => 'Procurement'],
            ['id' => 't4', 'date' => '2026-06-25', 'item_id' => 'c7', 'item_name' => 'Flotation Reagent', 'from_location' => 'HQ', 'to_station_id' => 'st-04', 'quantity' => 250, 'unit' => 'kg', 'status' => 'received', 'initiated_by' => 'Procurement'],
            ['id' => 't5', 'date' => '2026-06-24', 'item_id' => 'c2', 'item_name' => 'Engine Oil 15W-40', 'from_location' => 'HQ', 'to_station_id' => 'st-01', 'quantity' => 600, 'unit' => 'L', 'status' => 'received', 'initiated_by' => 'Procurement'],
        ];

        foreach ($transactions as $t) {
            Transaction::updateOrCreate(['id' => $t['id']], $t);
        }

        // Seed AppUsers (Users Table)
        $users = [
            ['id' => 'u1', 'name' => 'A. Ndlovu', 'email' => 'a.ndlovu@angrobeers.mine', 'role' => 'manager', 'station_id' => null, 'password' => Hash::make('password')],
            ['id' => 'u2', 'name' => 'T. Kabwe', 'email' => 't.kabwe@angrobeers.mine', 'role' => 'procurement', 'station_id' => null, 'password' => Hash::make('password')],
            ['id' => 'u3', 'name' => 'J. Mwale', 'email' => 'j.mwale@angrobeers.mine', 'role' => 'store_keeper', 'station_id' => 'st-01', 'password' => Hash::make('password')],
            ['id' => 'u4', 'name' => 'P. Banda', 'email' => 'p.banda@angrobeers.mine', 'role' => 'store_keeper', 'station_id' => 'st-02', 'password' => Hash::make('password')],
            ['id' => 'u5', 'name' => 'S. Lungu', 'email' => 's.lungu@angrobeers.mine', 'role' => 'site_manager', 'station_id' => 'st-02', 'password' => Hash::make('password')],
            ['id' => 'u6', 'name' => 'M. Zulu', 'email' => 'm.zulu@angrobeers.mine', 'role' => 'operation_manager', 'station_id' => 'st-04', 'password' => Hash::make('password')],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['id' => $u['id']], $u);
        }

        // Seed Alerts
        $alerts = [
            ['id' => 'al1', 'type' => 'low_stock', 'message' => 'Diesel Fuel below reorder level at Kitwe Pit A', 'severity' => 'high', 'at' => '2h ago'],
            ['id' => 'al2', 'type' => 'low_stock', 'message' => 'Hydraulic Oil low at Ndola Refinery', 'severity' => 'medium', 'at' => '5h ago'],
            ['id' => 'al3', 'type' => 'pending_arrival', 'message' => '8,000 L Diesel awaiting receipt at Kitwe Pit A', 'severity' => 'medium', 'at' => '1d ago'],
            ['id' => 'al4', 'type' => 'maintenance', 'message' => 'Komatsu HD785 (VH-1043) scheduled service overdue', 'severity' => 'high', 'at' => '1d ago'],
            ['id' => 'al5', 'type' => 'low_stock', 'message' => 'Flotation Reagent below reorder at Chingola Crusher', 'severity' => 'medium', 'at' => '2d ago'],
        ];

        foreach ($alerts as $al) {
            Alert::updateOrCreate(['id' => $al['id']], $al);
        }
    }
}
