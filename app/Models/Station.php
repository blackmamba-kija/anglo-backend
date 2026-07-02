<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'location',
        'manager',
        'status',
    ];

    public function consumables()
    {
        return $this->hasMany(ConsumableItem::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
