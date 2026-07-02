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
        // Only procurement role can create assets
        if ($request->user() && $request->user()->role !== 'procurement') {
            return response()->json(['message' => 'Forbidden: only procurement can register assets'], 403);
        }
        $validated = $request->validate([
            'id' => 'required|string|unique:assets,id',
            'tag' => 'required|string|unique:assets,tag',
            'name' => 'required|string',
            'type' => 'required|string',
            'station_id' => 'required|string|exists:stations,id',
            'status' => 'required|string',
            'assigned_to' => 'nullable|string',
        ]);

        $targetStationId = $validated['station_id'];
        $validated['station_id'] = 'hq';
        $asset = Asset::create($validated);

        // Create a pending inventory request for the destination station
        if (class_exists('\App\Models\InventoryRequest')) {
            \App\Models\InventoryRequest::create([
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
