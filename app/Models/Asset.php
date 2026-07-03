<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tag',
        'name',
        'type',
        'station_id',
        'status',
        'assigned_to',
        'serial_number',
        'model',
        'purchase_date',
        'purchase_cost',
        'description',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
