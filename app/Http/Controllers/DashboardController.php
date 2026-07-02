<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function stockUsageTrend()
    {
        $trend = [
            ['month' => 'Jan', 'diesel' => 32000, 'oil' => 1800, 'reagent' => 900],
            ['month' => 'Feb', 'diesel' => 34500, 'oil' => 2100, 'reagent' => 1100],
            ['month' => 'Mar', 'diesel' => 36800, 'oil' => 1950, 'reagent' => 1250],
            ['month' => 'Apr', 'diesel' => 35200, 'oil' => 2300, 'reagent' => 1180],
            ['month' => 'May', 'diesel' => 38900, 'oil' => 2500, 'reagent' => 1320],
            ['month' => 'Jun', 'diesel' => 41200, 'oil' => 2650, 'reagent' => 1410],
        ];

        return response()->json($trend);
    }
}
