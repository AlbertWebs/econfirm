<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Structured audit logging for developer-facing payment API calls.
 * Never log bearer tokens, consumer secrets, or security credentials.
 */
class PaymentGatewayAuditLog
{
    /**
     * @param  array<string, mixed>  $context
     */
    public static function record(string $event, Request $request, array $context = []): void
    {
        $user = $request->get('api_user');

        Log::info('payment_gateway_audit', array_merge([
            'event' => $event,
            'api_user_id' => $user?->id,
            'ip' => $request->ip(),
            'path' => $request->path(),
            'method' => $request->method(),
        ], $context));
    }
}
