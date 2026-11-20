<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShaftFuelEntry extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'station_id', 'shaft_name', 'kind', 'quantity', 'unit',
        'item_name', 'source_consumable_id', 'date', 'recorded_by', 'issued_to', 'purpose',
    ];

    protected $casts = [
        'quantity' => 'float',
        'date' => 'date:Y-m-d',
    ];
}
