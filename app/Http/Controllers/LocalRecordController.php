<?php

namespace App\Http\Controllers;

use App\Models\LocalRecord;
use Illuminate\Http\Request;

class LocalRecordController extends Controller
{
    public function index()
    {
        return response()->json(LocalRecord::all());
    }

    public function show($id)
    {
        $record = LocalRecord::find($id);
        if (!$record) {
            return response()->json(['message' => 'Local record not found'], 404);
        }
        return response()->json($record);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:local_records,id',
            'type' => 'required|string',
            'station_id' => 'required|string|exists:stations,id',
            'worker_name' => 'required|string',
            'item_id' => 'required|string',
            'item_name' => 'required|string',
            'quantity' => 'sometimes|integer|min:1',
            'unit' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);

        $record = LocalRecord::create($validated);
        return response()->json($record, 201);
    }

    public function update(Request $request, $id)
    {
        $record = LocalRecord::find($id);
        if (!$record) {
            return response()->json(['message' => 'Local record not found'], 404);
        }

        $validated = $request->validate([
            'worker_name' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|string',
        ]);

        $record->update($validated);
        return response()->json($record);
    }

    public function destroy($id)
    {
        $record = LocalRecord::find($id);
        if (!$record) {
            return response()->json(['message' => 'Local record not found'], 404);
        }
        $record->delete();
        return response()->json(['message' => 'Local record deleted successfully']);
    }
}
