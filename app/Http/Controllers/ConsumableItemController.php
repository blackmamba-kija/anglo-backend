<?php

namespace App\Http\Controllers;

use App\Models\ConsumableItem;
use Illuminate\Http\Request;

class ConsumableItemController extends Controller
{
    public function index()
    {
        return response()->json(ConsumableItem::all());
    }

    public function show($id)
    {
        $item = ConsumableItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Consumable item not found'], 404);
        }
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:consumable_items,id',
            'name' => 'required|string',
            'category' => 'required|string',
            'unit' => 'required|string',
            'station_id' => 'required|string|exists:stations,id',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $item = ConsumableItem::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = ConsumableItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Consumable item not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'category' => 'sometimes|string',
            'unit' => 'sometimes|string',
            'station_id' => 'sometimes|nullable|string',
            'quantity' => 'sometimes|integer|min:0',
            'reorder_level' => 'sometimes|integer|min:0',
        ]);

        $item->update($validated);
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = ConsumableItem::find($id);
        if (!$item) {
            return response()->json(['message' => 'Consumable item not found'], 404);
        }
        $item->delete();
        return response()->json(['message' => 'Consumable item deleted successfully']);
    }
}
