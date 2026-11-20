<?php

namespace App\Http\Controllers;

use App\Models\ShaftDailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShaftDailyReportController extends Controller
{
    private function rules(): array
    {
        return [
            'station_id' => 'required|string|exists:stations,id|not_in:hq',
            'shaft_name' => 'required|string|in:Main Shaft',
            'report_date' => 'required|date',
            'shift' => 'required|string|in:Day,Night',
            'supervisor_name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['draft', 'submitted'])],
            'production_tonnes' => 'required|numeric|min:0',
            'advance_meters' => 'required|numeric|min:0',
            'workforce_count' => 'required|integer|min:0',
            'downtime_hours' => 'required|numeric|min:0|max:24',
            'production_summary' => 'nullable|string|max:5000',
            'db_summary' => 'nullable|string|max:5000',
            'fuel_summary' => 'nullable|string|max:5000',
            'delivery_summary' => 'nullable|string|max:5000',
            'safety_summary' => 'nullable|string|max:5000',
            'equipment_summary' => 'nullable|string|max:5000',
            'challenges' => 'nullable|string|max:5000',
            'actions_taken' => 'nullable|string|max:5000',
            'next_shift_plan' => 'nullable|string|max:5000',
            'additional_notes' => 'nullable|string|max:5000',
            'source_snapshot' => 'nullable|array',
        ];
    }

    public function index(Request $request)
    {
        $validated = $request->validate(['station_id' => 'nullable|string|exists:stations,id']);
        $query = ShaftDailyReport::orderByDesc('report_date')->orderByDesc('created_at');
        if (isset($validated['station_id'])) $query->where('station_id', $validated['station_id']);
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['id'] = 'SDR-' . Str::uuid();
        $validated['submitted_at'] = $validated['status'] === 'submitted' ? now() : null;
        $report = ShaftDailyReport::create($validated);
        return response()->json($report, 201);
    }

    public function update(Request $request, string $id)
    {
        $report = ShaftDailyReport::findOrFail($id);
        $validated = $request->validate($this->rules());
        $validated['submitted_at'] = $validated['status'] === 'submitted'
            ? ($report->submitted_at ?? now()) : null;
        $report->update($validated);
        return response()->json($report->fresh());
    }
}
