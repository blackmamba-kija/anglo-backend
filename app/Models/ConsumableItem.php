<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableItem extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'category',
        'unit',
        'station_id',
        'quantity',
        'reorder_level',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
