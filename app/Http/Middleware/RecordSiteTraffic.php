<?php

namespace App\Http\Middleware;

use App\Models\SitePageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordSiteTraffic
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $this->shouldRecord($request, $response)) {
            return;
        }

        $path = $request->path();
        if (strlen($path) > 500) {
            $path = substr($path, 0, 500);
        }

        SitePageView::query()->create([
            'path' => $path,
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
            'created_at' => now(),
        ]);
    }

    protected function shouldRecord(Request $request, Response $response): bool
    {
        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if ($request->is('admin', 'admin/*')) {
            return false;
        }

        if ($request->is('storage', 'storage/*', 'up', 'sanctum/csrf-cookie')) {
            return false;
        }

        if ($request->is('telescope', 'telescope/*', 'horizon', 'horizon/*', '_debugbar/*')) {
            return false;
        }

        if ($request->is('test-sms', 'test-sms/*')) {
            return false;
        }

        if ($this->isLikelyBot($request->userAgent() ?? '')) {
            return false;
        }

        return true;
    }

    protected function isLikelyBot(string $ua): bool
    {
        $needle = strtolower($ua);
        if ($needle === '') {
            return false;
        }

        $bots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandexbot',
            'facebot', 'facebookexternalhit', 'twitterbot', 'linkedinbot', 'semrushbot',
            'ahrefsbot', 'petalbot', 'bytespider', 'amazonbot', 'applebot', 'gptbot',
            'chatgpt-user', 'claudebot', 'anthropic-ai', 'perplexitybot', 'ccbot',
        ];

        foreach ($bots as $bot) {
            if (str_contains($needle, $bot)) {
                return true;
            }
        }

        return false;
    }
}
