<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * List logs with filtering and pagination.
     * Query params: module, action, severity, user_id, station_id, from, to, per_page
     */
    public function index(Request $request)
    {
        $query = ActivityLog::orderBy('created_at', 'desc');

        if ($request->filled('module'))     $query->where('module', $request->module);
        if ($request->filled('action'))     $query->where('action', $request->action);
        if ($request->filled('severity'))   $query->where('severity', $request->severity);
        if ($request->filled('user_id'))    $query->where('user_id', $request->user_id);
        if ($request->filled('station_id')) $query->where('station_id', $request->station_id);
        if ($request->filled('search'))     $query->where(function ($q) use ($request) {
            $q->where('user_name', 'like', '%' . $request->search . '%')
              ->orWhere('target_name', 'like', '%' . $request->search . '%')
              ->orWhere('action', 'like', '%' . $request->search . '%')
              ->orWhere('module', 'like', '%' . $request->search . '%');
        });
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        $perPage = min((int) $request->get('per_page', 50), 200);
        return response()->json($query->paginate($perPage));
    }

    /**
     * Create a new log entry (called from frontend after every action).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'nullable|string',
            'user_name'   => 'nullable|string',
            'user_role'   => 'nullable|string',
            'action'      => 'required|string',
            'module'      => 'required|string',
            'target_id'   => 'nullable|string',
            'target_name' => 'nullable|string',
            'changes'     => 'nullable|array',
            'station_id'  => 'nullable|string',
            'severity'    => 'nullable|string|in:info,warning,critical',
        ]);

        $validated['ip_address'] = $request->ip();

        $log = ActivityLog::create($validated);
        return response()->json($log, 201);
    }

    /**
     * Delete a single log entry (admin only).
     */
    public function destroy($id)
    {
        $log = ActivityLog::find($id);
        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }
        $log->delete();
        return response()->json(['message' => 'Log deleted']);
    }

    /**
     * Clear all logs or filtered logs.
     * Query params: module, severity, before (date)
     */
    public function clear(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('module'))   $query->where('module', $request->module);
        if ($request->filled('severity')) $query->where('severity', $request->severity);
        if ($request->filled('before'))   $query->whereDate('created_at', '<=', $request->before);

        $count = $query->count();
        $query->delete();

        return response()->json(['message' => "Deleted {$count} log entries", 'count' => $count]);
    }

    /**
     * Summary statistics for the logs dashboard widget.
     */
    public function stats()
    {
        return response()->json([
            'total'    => ActivityLog::count(),
            'today'    => ActivityLog::whereDate('created_at', today())->count(),
            'critical' => ActivityLog::where('severity', 'critical')->count(),
            'warning'  => ActivityLog::where('severity', 'warning')->count(),
            'by_module' => ActivityLog::selectRaw('module, count(*) as count')
                ->groupBy('module')
                ->orderByDesc('count')
                ->get(),
            'by_action' => ActivityLog::selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->orderByDesc('count')
                ->get(),
        ]);
    }
}
