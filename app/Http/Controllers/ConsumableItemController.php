<?php

namespace App\Http\Controllers;

use App\Models\ConsumableItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $this->normalizeFuel($request);
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

        $this->normalizeFuel($request, $item);
        $validated = $request->validate([
            'name'          => 'sometimes|string',
            'category'      => 'sometimes|string',
            'unit'          => 'sometimes|string',
            'quantity'      => 'sometimes|integer|min:0',
            'reorder_level' => 'sometimes|integer|min:0',
            // station_id intentionally omitted — not updatable after creation
        ]);
        // Strip any station_id sent by the client to avoid exists:stations validation issues
        unset($validated['station_id']);

        $item->update($validated);
        return response()->json($item);
    }

    private function normalizeFuel(Request $request, ?ConsumableItem $existing = null): void
    {
        $category = trim((string) $request->input('category', $existing?->category ?? ''));
        $name = trim((string) $request->input('name', $existing?->name ?? ''));
        $label = strtolower($category . ' ' . $name);
        $looksLikeFuel = str_contains($label, 'fuel')
            || str_contains($label, 'diesel')
            || str_contains($label, 'diezel')
            || str_contains($label, 'petrol');

        if (!$looksLikeFuel) {
            return;
        }

        if (str_contains($label, 'petrol')) {
            $fuelType = 'Petrol';
        } elseif (str_contains($label, 'diesel') || str_contains($label, 'diezel')) {
            $fuelType = 'Diesel';
        } else {
            throw ValidationException::withMessages([
                'category' => 'Select a fuel type: Diesel or Petrol.',
            ]);
        }

        $request->merge([
            'name' => $fuelType,
            'category' => $fuelType,
            'unit' => 'L',
        ]);
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
