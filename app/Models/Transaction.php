<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'date',
        'item_id',
        'item_name',
        'from_location',
        'to_station_id',
        'quantity',
        'unit',
        'status',
        'initiated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function toStation()
    {
        return $this->belongsTo(Station::class, 'to_station_id');
    }
}
