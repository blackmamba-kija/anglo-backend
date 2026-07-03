<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'station_id',
        'quantity',
        'unit',
        'reorder_level',
        'status',
        'requested_by',
        'asset_id',
        'consumable_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reorder_level' => 'integer',
    ];

    // Relationships
    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // public function asset()
    // {
    //     return $this->belongsTo(Asset::class);
    // }
}
?>
