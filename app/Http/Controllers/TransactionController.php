<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ConsumableItem;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::orderBy('date', 'desc')->get());
    }

    public function show($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        return response()->json($transaction);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:transactions,id',
            'date' => 'required|date',
            'item_id' => 'required|string',
            'item_name' => 'required|string',
            'from_location' => 'required|string',
            'to_station_id' => 'required|string|exists:stations,id',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'status' => 'required|string|in:pending,received,rejected',
            'initiated_by' => 'required|string',
        ]);

        $transaction = Transaction::create($validated);

        // If transaction is created directly as received, update stock
        if ($validated['status'] === 'received') {
            $this->incrementConsumableStock($transaction);
        }

        return response()->json($transaction, 201);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,received,rejected',
        ]);

        $oldStatus = $transaction->status;
        $transaction->update($validated);

        // If status transitioned to received, increment consumable stock
        if ($validated['status'] === 'received' && $oldStatus !== 'received') {
            $this->incrementConsumableStock($transaction);
        }

        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted successfully']);
    }

    private function incrementConsumableStock(Transaction $transaction)
    {
        $consumable = ConsumableItem::where('station_id', $transaction->to_station_id)
            ->where('name', $transaction->item_name)
            ->first();

        if ($consumable) {
            $consumable->quantity += $transaction->quantity;
            $consumable->save();
        } else {
            // Generate a unique ID for new consumable
            ConsumableItem::create([
                'id' => 'c-' . substr(md5(uniqid()), 0, 8),
                'name' => $transaction->item_name,
                'category' => 'Consumable',
                'unit' => $transaction->unit,
                'station_id' => $transaction->to_station_id,
                'quantity' => $transaction->quantity,
                'reorder_level' => 100,
            ]);
        }
    }
}
