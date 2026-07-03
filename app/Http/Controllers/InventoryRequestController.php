<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\InventoryRequest;
use App\Models\Asset;
use App\Models\ConsumableItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryRequestController extends Controller
{
    /**
     * Display a listing of the inventory requests.
     * Optional query param: station_id to filter for a specific station.
     */
    public function index(Request $request)
    {
        $query = InventoryRequest::query();
        if ($request->has('station_id')) {
            $query->where('station_id', $request->query('station_id'));
        }
        return response()->json($query->get());
    }

    /**
     * Store a newly created inventory request.
     * Expected payload: asset_id, station_id.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'asset_id'       => 'sometimes|string|exists:assets,id',
            'consumable_id'  => 'sometimes|string|exists:consumable_items,id',
            'station_id'     => 'required|string|exists:stations,id',
            'quantity'       => 'sometimes|integer|min:1',
        ]);

        if (!($validated['asset_id'] ?? null) && !($validated['consumable_id'] ?? null)) {
            return response()->json(['message' => 'Either asset_id or consumable_id must be provided'], 400);
        }
        $userId = $request->user()?->id ?? 1; // fallback for unauthenticated requests

        if (isset($validated['asset_id'])) {
            $asset = Asset::findOrFail($validated['asset_id']);
            $name = $asset->name;
            $category = $asset->type;
        } elseif (isset($validated['consumable_id'])) {
            $consumable = ConsumableItem::findOrFail($validated['consumable_id']);
            $name = $consumable->name;
            $category = $consumable->category;
        } else {
            $name = 'Unknown';
            $category = 'unknown';
        }

        // Create inventory request record
        $invRequest = InventoryRequest::create([
            'name'           => $name,
            'category'       => $category,
            'station_id'     => $validated['station_id'],
            'quantity'       => $validated['quantity'] ?? 1,
            'unit'           => 'unit',
            'reorder_level'  => 0,
            'status'         => 'pending',
            'requested_by'   => $userId,
            'asset_id'       => $validated['asset_id'] ?? null,
            'consumable_id'  => $validated['consumable_id'] ?? null,
        ]);

        // Also create a Transaction record so it shows up on the Distribution page tabs
        if (class_exists('\App\Models\Transaction')) {
            \App\Models\Transaction::create([
                'id'            => 't_ir_' . $invRequest->id,
                'date'          => date('Y-m-d'),
                'item_id'       => $validated['asset_id'] ?? $validated['consumable_id'],
                'item_name'     => $name,
                'from_location' => 'HQ',
                'to_station_id' => $validated['station_id'],
                'quantity'      => $validated['quantity'] ?? 1,
                'unit'          => isset($validated['asset_id']) ? 'unit' : ($consumable->unit ?? 'unit'),
                'status'        => 'pending',
                'initiated_by'  => $request->user()?->name ?? 'Procurement',
            ]);
        }

        // Deduct from inventory if consumable
        if (isset($validated['consumable_id'])) {
            $deductQty = $validated['quantity'] ?? 1;
            // Ensure enough stock
            if ($consumable->quantity < $deductQty) {
                return response()->json(['message' => 'Insufficient consumable quantity'], 400);
            }
            $consumable->quantity -= $deductQty;
            $consumable->save();
        }

        return response()->json($invRequest, 201);
    }

    /**
     * Accept a pending inventory request.
     * Storekeeper calls this to add the item/quantity to their station.
     */
    public function accept(Request $request, $id)
    {
        $invRequest = InventoryRequest::findOrFail($id);
        if ($invRequest->status !== 'pending') {
            return response()->json(['message' => 'Request cannot be accepted'], 400);
        }

        // Handle consumable acceptance
        if ($invRequest->consumable_id) {
            $sourceConsumable = ConsumableItem::find($invRequest->consumable_id);
            if (!$sourceConsumable) {
                return response()->json(['message' => 'Source consumable not found'], 404);
            }

            // Create or update consumable record for destination station
            $destConsumable = ConsumableItem::where('station_id', $invRequest->station_id)
                ->where('name', $sourceConsumable->name)
                ->first();
            if ($destConsumable) {
                $destConsumable->quantity += $invRequest->quantity ?? 1;
                $destConsumable->save();
            } else {
                // create new consumable entry for destination station
                $destConsumable = new ConsumableItem();
                $destConsumable->id = (string) \Illuminate\Support\Str::uuid();
                $destConsumable->name = $sourceConsumable->name;
                $destConsumable->category = $sourceConsumable->category;
                $destConsumable->unit = $sourceConsumable->unit ?? 'unit';
                $destConsumable->station_id = $invRequest->station_id;
                $destConsumable->quantity = $invRequest->quantity ?? 1;
                $destConsumable->reorder_level = $sourceConsumable->reorder_level ?? 0;
                $destConsumable->save();
            }

            // If HQ source consumable quantity is now 0 (fully dispatched), remove it from HQ
            // so the item no longer appears in HQ stock. Partial dispatches leave remaining qty.
            if ($sourceConsumable->quantity <= 0) {
                $sourceConsumable->delete();
            }
        }

        // Handle asset acceptance
        if ($invRequest->asset_id) {
            $asset = Asset::find($invRequest->asset_id);
            if ($asset) {
                $asset->station_id = $invRequest->station_id;
                $asset->save();
            }
        }

        $invRequest->status = 'accepted';
        $invRequest->save();

        // Update corresponding Transaction status
        if (class_exists('\App\Models\Transaction')) {
            $transaction = \App\Models\Transaction::find('t_ir_' . $id);
            if ($transaction) {
                $transaction->status = 'received';
                $transaction->save();
            }
        }

        return response()->json($invRequest);
    }

    /**
     * Reject a pending inventory request.
     * Storekeeper calls this to reject the shipment, which refunds stock if it is a consumable.
     */
    public function reject(Request $request, $id)
    {
        $invRequest = InventoryRequest::findOrFail($id);
        if ($invRequest->status !== 'pending') {
            return response()->json(['message' => 'Request cannot be rejected'], 400);
        }

        // Handle consumable rejection (return stock to source HQ)
        if ($invRequest->consumable_id) {
            $sourceConsumable = ConsumableItem::find($invRequest->consumable_id);
            if ($sourceConsumable) {
                // Source still exists — add back the dispatched quantity
                $sourceConsumable->quantity += $invRequest->quantity ?? 1;
                $sourceConsumable->save();
            } else {
                // Source was deleted after a full dispatch — recreate the HQ entry
                // using the request's stored name/category as fallback
                $restored = new ConsumableItem();
                $restored->id = $invRequest->consumable_id; // restore original ID
                $restored->name = $invRequest->name;
                $restored->category = $invRequest->category;
                $restored->unit = 'unit';
                $restored->station_id = 'hq';
                $restored->quantity = $invRequest->quantity ?? 1;
                $restored->reorder_level = 0;
                $restored->save();
            }
        }

        $invRequest->status = 'rejected';
        $invRequest->save();

        // Update corresponding Transaction status
        if (class_exists('\App\Models\Transaction')) {
            $transaction = \App\Models\Transaction::find('t_ir_' . $id);
            if ($transaction) {
                $transaction->status = 'rejected';
                $transaction->save();
            }
        }

        return response()->json($invRequest);
    }
}
?>
