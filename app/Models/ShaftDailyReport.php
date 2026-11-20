<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShaftDailyReport extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'station_id', 'shaft_name', 'report_date', 'shift', 'supervisor_name',
        'status', 'production_tonnes', 'advance_meters', 'workforce_count',
        'downtime_hours', 'production_summary', 'db_summary', 'fuel_summary',
        'delivery_summary', 'safety_summary', 'equipment_summary', 'challenges',
        'actions_taken', 'next_shift_plan', 'additional_notes', 'source_snapshot',
        'submitted_at',
    ];

    protected $casts = [
        'report_date' => 'date:Y-m-d',
        'submitted_at' => 'datetime',
        'source_snapshot' => 'array',
        'production_tonnes' => 'float',
        'advance_meters' => 'float',
        'workforce_count' => 'integer',
        'downtime_hours' => 'float',
    ];
}
