<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntariousDemoRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'company',
        'role',
        'team_size',
        'use_case',
        'ip_address',
        'user_agent',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];
}
