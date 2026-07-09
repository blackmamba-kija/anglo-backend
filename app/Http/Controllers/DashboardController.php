<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function stockUsageTrend()
    {
        $trend = [];
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
        }

        foreach ($months as $month) {
            $records = \App\Models\LocalRecord::where('type', 'issue_stock')
                ->where('created_at', 'like', $month . '%')
                ->get();
            
            $diesel = $records->filter(function($r) { return str_contains(strtolower($r->item_name), 'diesel'); })->sum('quantity');
            $oil = $records->filter(function($r) { return str_contains(strtolower($r->item_name), 'oil') || str_contains(strtolower($r->item_name), 'petrol'); })->sum('quantity');
            $reagent = $records->filter(function($r) { return str_contains(strtolower($r->item_name), 'maji') || str_contains(strtolower($r->item_name), 'reagent') || str_contains(strtolower($r->item_name), 'kuywa'); })->sum('quantity');

            $trend[] = [
                'month' => \Carbon\Carbon::parse($month . '-01')->format('M'),
                'diesel' => $diesel,
                'oil' => $oil,
                'reagent' => $reagent
            ];
        }

        return response()->json($trend);
    }
}
