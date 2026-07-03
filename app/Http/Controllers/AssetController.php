<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return response()->json(Asset::all());
    }

    public function show($id)
    {
        $asset = Asset::find($id);
        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }
        return response()->json($asset);
    }

    public function store(Request $request)
    {
        // Only procurement and manager roles can create assets
        if ($request->user() && !in_array($request->user()->role, ['procurement', 'manager'])) {
            return response()->json(['message' => 'Forbidden: only procurement and managers can register assets'], 403);
        }
        $validated = $request->validate([
            'id' => 'required|string|unique:assets,id',
            'tag' => 'required|string|unique:assets,tag',
            'name' => 'required|string',
            'type' => 'required|string',
            'station_id' => 'required|string|exists:stations,id',
            'status' => 'required|string',
            'assigned_to' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'model' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $targetStationId = $validated['station_id'];
        $validated['station_id'] = 'hq';
        $asset = Asset::create($validated);

        // Create a pending inventory request for the destination station
        if (class_exists('\App\Models\InventoryRequest')) {
            $invRequest = \App\Models\InventoryRequest::create([
                'name' => $asset->name,
                'category' => $asset->type,
                'station_id' => $targetStationId,
                'quantity' => 1,
                'unit' => 'unit',
                'reorder_level' => 0,
                'status' => 'pending',
                'requested_by' => $request->user()?->id ?? 1,
                'asset_id' => $asset->id,
            ]);

            // Also create a Transaction record so it shows up on the Distribution page tabs
            if (class_exists('\App\Models\Transaction')) {
                \App\Models\Transaction::create([
                    'id'            => 't_ir_' . $invRequest->id,
                    'date'          => date('Y-m-d'),
                    'item_id'       => $asset->id,
                    'item_name'     => $asset->name,
                    'from_location' => 'HQ',
                    'to_station_id' => $targetStationId,
                    'quantity'      => 1,
                    'unit'          => 'unit',
                    'status'        => 'pending',
                    'initiated_by'  => $request->user()?->name ?? 'Procurement',
                ]);
            }
        }

        return response()->json($asset, 201);
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::find($id);
        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        $validated = $request->validate([
            'tag' => 'sometimes|string|unique:assets,tag,' . $id,
            'name' => 'sometimes|string',
            'type' => 'sometimes|string',
            'station_id' => 'sometimes|string|exists:stations,id',
            'status' => 'sometimes|string',
            'assigned_to' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'model' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $asset->update($validated);
        return response()->json($asset);
    }

    public function destroy($id)
    {
        $asset = Asset::find($id);
        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }
        $asset->delete();
        return response()->json(['message' => 'Asset deleted successfully']);
    }
}
