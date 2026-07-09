<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'target_id',
        'target_name',
        'changes',
        'station_id',
        'ip_address',
        'severity',
    ];

    protected $casts = [
        'changes' => 'array',
    ];
}
