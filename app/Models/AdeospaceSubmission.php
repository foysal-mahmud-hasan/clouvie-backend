<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeospaceSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'organisation',
        'sector',
        'message',
        'ip_address',
        'user_agent',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];
}
