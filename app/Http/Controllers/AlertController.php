<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = [];
        $index = 1;

        // 1. Low stock consumables (where quantity < reorder_level and reorder_level > 0)
        $consumables = \App\Models\ConsumableItem::with('station')->get();
        foreach ($consumables as $item) {
            if ($item->reorder_level > 0 && $item->quantity < $item->reorder_level) {
                $stationName = $item->station ? $item->station->name : "Station {$item->station_id}";
                $severity = $item->quantity < ($item->reorder_level / 2) ? 'high' : 'medium';
                $alerts[] = [
                    'id' => 'al_stock_' . $item->id,
                    'type' => 'low_stock',
                    'message' => "{$item->name} below reorder level ({$item->quantity}/{$item->reorder_level} {$item->unit}) at {$stationName}",
                    'severity' => $severity,
                    'at' => 'just now',
                    'created_at' => now()->toIso8601String(),
                ];
            }
        }

        // 2. Pending arrivals/transactions (where status == 'pending')
        $transactions = \App\Models\Transaction::where('status', 'pending')->get();
        foreach ($transactions as $tx) {
            $alerts[] = [
                'id' => 'al_tx_' . $tx->id,
                'type' => 'pending_arrival',
                'message' => "{$tx->quantity} {$tx->unit} of {$tx->item_name} awaiting receipt from {$tx->from_location}",
                'severity' => 'medium',
                'at' => 'in transit',
                'created_at' => $tx->created_at ? $tx->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        }

        // 3. Maintenance assets (where status == 'maintenance')
        $assets = \App\Models\Asset::where('status', 'maintenance')->get();
        foreach ($assets as $asset) {
            $alerts[] = [
                'id' => 'al_asset_' . $asset->id,
                'type' => 'maintenance',
                'message' => "{$asset->name} ({$asset->tag}) is currently scheduled for maintenance",
                'severity' => 'high',
                'at' => 'active',
                'created_at' => $asset->created_at ? $asset->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        }

        return response()->json($alerts);
    }

    public function show($id)
    {
        $alert = Alert::find($id);
        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }
        return response()->json($alert);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:alerts,id',
            'type' => 'required|string',
            'message' => 'required|string',
            'severity' => 'required|string|in:high,medium,low',
            'at' => 'required|string',
        ]);

        $alert = Alert::create($validated);
        return response()->json($alert, 201);
    }

    public function update(Request $request, $id)
    {
        $alert = Alert::find($id);
        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }

        $validated = $request->validate([
            'type' => 'sometimes|string',
            'message' => 'sometimes|string',
            'severity' => 'sometimes|string|in:high,medium,low',
            'at' => 'sometimes|string',
        ]);

        $alert->update($validated);
        return response()->json($alert);
    }

    public function destroy($id)
    {
        $alert = Alert::find($id);
        if (!$alert) {
            return response()->json(['message' => 'Alert not found'], 404);
        }
        $alert->delete();
        return response()->json(['message' => 'Alert deleted successfully']);
    }
}
