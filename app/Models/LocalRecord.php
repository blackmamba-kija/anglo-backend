<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalRecord extends Model
{
    use HasFactory;

    protected $table = 'local_records';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'station_id',
        'worker_name',
        'item_id',
        'item_name',
        'quantity',
        'unit',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
}
