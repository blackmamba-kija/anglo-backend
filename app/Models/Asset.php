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
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
