<?php

namespace App\Http\Controllers;

use App\Models\ConsumableItem;
use App\Models\ShaftFuelEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShaftFuelController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'nullable|string|exists:stations,id',
        ]);

        $query = ShaftFuelEntry::orderByDesc('date')->orderByDesc('created_at');
        if (isset($validated['station_id'])) {
            $query->where('station_id', $validated['station_id']);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'required|string|exists:stations,id|not_in:hq',
            'shaft_name' => 'required|string|in:Main Shaft',
            'kind' => 'required|string|in:received,used',
            'quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'recorded_by' => 'required|string|max:255',
            'issued_to' => 'required_if:kind,used|nullable|string|max:255',
            'purpose' => 'nullable|string|max:500',
            'source_consumable_id' => 'required_if:kind,received|nullable|string|exists:consumable_items,id',
        ]);

        $entry = DB::transaction(function () use ($validated) {
            if ($validated['kind'] === 'received') {
                $source = ConsumableItem::lockForUpdate()->findOrFail($validated['source_consumable_id']);
                if ($source->station_id !== 'hq') {
                    throw ValidationException::withMessages(['source_consumable_id' => 'Fuel must be dispatched from HQ stock.']);
                }
                $isFuel = str_contains(strtolower($source->category), 'fuel')
                    || str_contains(strtolower($source->category), 'diesel')
                    || str_contains(strtolower($source->category), 'petrol')
                    || str_contains(strtolower($source->name), 'fuel')
                    || str_contains(strtolower($source->name), 'diesel')
                    || str_contains(strtolower($source->name), 'diezel')
                    || str_contains(strtolower($source->name), 'petrol')
                    || str_contains(strtolower($source->name), 'mafuta');
                if (!$isFuel) {
                    throw ValidationException::withMessages(['source_consumable_id' => 'Only fuel can be dispatched to a shaft.']);
                }
                if ($source->quantity < $validated['quantity']) {
                    throw ValidationException::withMessages(['quantity' => 'Insufficient fuel at HQ.']);
                }
                $source->decrement('quantity', $validated['quantity']);
                $validated['item_name'] = $source->name;
                $validated['unit'] = $source->unit;
            } else {
                $balance = ShaftFuelEntry::where('station_id', $validated['station_id'])
                    ->where('shaft_name', $validated['shaft_name'])
                    ->selectRaw("COALESCE(SUM(CASE WHEN kind = 'received' THEN quantity ELSE -quantity END), 0) as balance")
                    ->value('balance');
                if ((float) $balance < (float) $validated['quantity']) {
                    throw ValidationException::withMessages(['quantity' => 'Insufficient fuel at this shaft.']);
                }
                $validated['item_name'] = 'Shaft Fuel';
                $validated['unit'] = 'L';
            }

            $validated['id'] = 'SFE-' . Str::uuid();
            return ShaftFuelEntry::create($validated);
        });

        return response()->json($entry, 201);
    }
}
