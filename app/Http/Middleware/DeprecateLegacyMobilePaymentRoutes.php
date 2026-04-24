<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Legacy /api/mobile/payment/* flows: still supported but partners should migrate to POST /api/v1/payments/*.
 */
class DeprecateLegacyMobilePaymentRoutes
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $v1 = rtrim((string) config('app.url'), '/').'/api/v1/payments/stk-push';
            $response->headers->set('Deprecation', 'true');
            $response->headers->set('Link', '<'.$v1.'>; rel="successor-version"');
        }

        return $response;
    }
}
