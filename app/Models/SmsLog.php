<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'recipient',
        'sender',
        'correlator',
        'message',
        'is_success',
        'provider_message',
        'provider_unique_id',
        'http_code',
        'ip_address',
        'user_agent',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
