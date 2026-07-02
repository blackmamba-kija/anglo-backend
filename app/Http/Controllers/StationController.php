<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        return response()->json(Station::all());
    }

    public function show($id)
    {
        $station = Station::find($id);
        if (!$station) {
            return response()->json(['message' => 'Station not found'], 404);
        }
        return response()->json($station);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:stations,id',
            'name' => 'required|string',
            'location' => 'required|string',
            'manager' => 'required|string',
            'status' => 'required|string',
        ]);

        $station = Station::create($validated);
        return response()->json($station, 201);
    }

    public function update(Request $request, $id)
    {
        $station = Station::find($id);
        if (!$station) {
            return response()->json(['message' => 'Station not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'location' => 'sometimes|string',
            'manager' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);

        $station->update($validated);
        return response()->json($station);
    }

    public function destroy($id)
    {
        $station = Station::find($id);
        if (!$station) {
            return response()->json(['message' => 'Station not found'], 404);
        }
        $station->delete();
        return response()->json(['message' => 'Station deleted successfully']);
    }
}
