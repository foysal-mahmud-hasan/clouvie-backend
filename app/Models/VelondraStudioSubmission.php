<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VelondraStudioSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'company',
        'budget',
        'services',
        'message',
        'ip_address',
        'user_agent',
        'notified_at',
    ];

    protected $casts = [
        'services' => 'array',
        'notified_at' => 'datetime',
    ];
}
