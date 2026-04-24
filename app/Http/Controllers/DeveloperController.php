<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\MpesaCallbackUrls;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DeveloperController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $v1 = econfirm_api_v1_url();
        $apiRoot = econfirm_api_root_url();

        $keyPreview = null;
        if (filled($user->api_key)) {
            $k = (string) $user->api_key;
            $keyPreview = strlen($k) > 12 ? substr($k, 0, 7).'…'.substr($k, -4) : '••••••••';
        }

        $apiTransactions = Transaction::query()
            ->where('api_user_id', $user->id)
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id', 'transaction_id', 'status', 'transaction_amount', 'currency', 'created_at']);

        $bearer = session('new_api_key') ?? ($user->api_key ?: 'YOUR_API_KEY');
        $sampleTxn = $apiTransactions->first()?->transaction_id ?? 'txn_xxxxxxxx';

        return view('dashboard.developer', [
            'apiV1Url' => $v1,
            'apiRootUrl' => $apiRoot,
            'keyPreview' => $keyPreview,
            'storedApiKey' => filled($user->api_key) ? (string) $user->api_key : null,
            'apiTransactions' => $apiTransactions,
            'codeSamples' => $this->buildCodeSamples($apiRoot, $v1, $bearer, $sampleTxn),
            'mpesaCallbackUrls' => MpesaCallbackUrls::adminSummary(),
            'apiUsage' => $this->buildApiUsageForUser((int) $user->id),
        ]);
    }

    /**
     * Escrow rows created via the API key (api_user_id). Not a raw HTTP request log.
     *
     * @return array{
     *   totals: array{all_time: int, last_7d: int, last_30d: int, this_month: int, last_month: int, first_at: ?Carbon, last_at: ?Carbon},
     *   by_currency: list<array{currency: string, count: int, volume: float}>,
     *   currency_volume_max: float,
     *   status_breakdown: list<array{status: string, count: int, pct: float}>,
     *   daily: list<array{label: string, dow: string, count: int}>,
     *   max_daily: int,
     *   throttle: array{limit: int, per: int}
     * }
     */
    protected function buildApiUsageForUser(int $userId): array
    {
        $base = Transaction::query()->where('api_user_id', $userId);

        $total = (clone $base)->count();
        $last7 = (clone $base)->where('created_at', '>=', now()->subDays(7))->count();
        $last30 = (clone $base)->where('created_at', '>=', now()->subDays(30))->count();
        $thisMonth = (clone $base)->where('created_at', '>=', now()->copy()->startOfMonth())->count();
        $lastMonthStart = now()->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->copy()->subMonth()->endOfMonth();
        $lastMonth = (clone $base)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $firstRaw = (clone $base)->min('created_at');
        $lastRaw = (clone $base)->max('created_at');

        $byCurrency = (clone $base)
            ->selectRaw('COALESCE(NULLIF(TRIM(currency), ""), "KES") as curr, COUNT(*) as cnt, SUM(transaction_amount) as vol')
            ->groupByRaw('COALESCE(NULLIF(TRIM(currency), ""), "KES")')
            ->orderByDesc('vol')
            ->get()
            ->map(fn ($r) => [
                'currency' => (string) $r->curr,
                'count' => (int) $r->cnt,
                'volume' => round((float) $r->vol, 2),
            ])
            ->values()
            ->all();

        $currencyVolumeMax = (float) (collect($byCurrency)->max('volume') ?: 1);

        $statusRows = (clone $base)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->orderByDesc('c')
            ->get();

        $statusSum = max(1, (int) $statusRows->sum('c'));
        $statusBreakdown = $statusRows->map(function ($r) use ($statusSum) {
            $c = (int) $r->c;

            return [
                'status' => (string) ($r->status ?? 'unknown'),
                'count' => $c,
                'pct' => round(100 * $c / $statusSum, 1),
            ];
        })->values()->all();

        $daily = [];
        $maxDaily = 0;
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->copy()->subDays($i)->startOfDay();
            $c = (clone $base)->whereDate('created_at', $day->toDateString())->count();
            $maxDaily = max($maxDaily, $c);
            $daily[] = [
                'label' => $day->format('M j'),
                'dow' => $day->format('D'),
                'count' => $c,
            ];
        }

        return [
            'totals' => [
                'all_time' => $total,
                'last_7d' => $last7,
                'last_30d' => $last30,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'first_at' => $firstRaw ? Carbon::parse($firstRaw) : null,
                'last_at' => $lastRaw ? Carbon::parse($lastRaw) : null,
            ],
            'by_currency' => $byCurrency,
            'currency_volume_max' => $currencyVolumeMax,
            'status_breakdown' => $statusBreakdown,
            'daily' => $daily,
            'max_daily' => max($maxDaily, 1),
            'throttle' => ['limit' => 1000, 'per' => 60],
        ];
    }

    /**
     * Postman Collection v2.1 — Escrow v1 + health check (variables: apiRoot, apiKey).
     */
    public function downloadPostmanCollection(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $apiRoot = econfirm_api_root_url();
        $apiV1 = econfirm_api_v1_url();
        $key = (string) ($user->api_key ?? '');
        if ($key === '') {
            return redirect()->route('api.home')
                ->with('error', 'Generate an API key first, then download the Postman collection.');
        }

        $collection = $this->postmanCollectionArray($apiRoot, $apiV1, $key);
        $json = json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return response($json, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="econfirm-escrow-api.postman_collection.json"',
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function buildCodeSamples(string $apiRoot, string $apiV1, string $bearer, string $sampleTxn): array
    {
        $createJson = '{"buyer_email":"buyer@example.com","seller_email":"seller@example.com","amount":1500.5,"currency":"KES","description":"API escrow","terms":"Deliver on receipt of funds."}';
        $releaseJson = '{"confirmation_code":"YOUR_CONFIRMATION_CODE","notes":"Optional note"}';

        $curlPing = "curl -sS \"{$apiRoot}/ping\"";
        $curlCreate = "curl -sS -X POST \"{$apiV1}/transactions\" \\\n  -H \"Authorization: Bearer {$bearer}\" \\\n  -H \"Content-Type: application/json\" \\\n  -H \"Accept: application/json\" \\\n  -d '{$createJson}'";
        $curlGet = "curl -sS \"{$apiV1}/transactions/{$sampleTxn}\" \\\n  -H \"Authorization: Bearer {$bearer}\" \\\n  -H \"Accept: application/json\"";
        $curlRelease = "curl -sS -X POST \"{$apiV1}/transactions/{$sampleTxn}/release\" \\\n  -H \"Authorization: Bearer {$bearer}\" \\\n  -H \"Content-Type: application/json\" \\\n  -H \"Accept: application/json\" \\\n  -d '{$releaseJson}'";

        $js = <<<JS
const API_KEY = "{$bearer}";
const v1 = "{$apiV1}";

// Health (no auth)
await fetch("{$apiRoot}/ping").then(r => r.json());

// Create escrow
const created = await fetch(\`\${v1}/transactions\`, {
  method: "POST",
  headers: {
    "Authorization": \`Bearer \${API_KEY}\`,
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
  body: JSON.stringify({
    buyer_email: "buyer@example.com",
    seller_email: "seller@example.com",
    amount: 1500.5,
    currency: "KES",
    description: "API escrow",
    terms: "Deliver on receipt of funds.",
  }),
}).then(r => r.json());

// Get status
await fetch(\`\${v1}/transactions/{$sampleTxn}\`, {
  headers: { Authorization: \`Bearer \${API_KEY}\`, Accept: "application/json" },
}).then(r => r.json());

// Release (requires valid confirmation_code on the transaction)
await fetch(\`\${v1}/transactions/{$sampleTxn}/release\`, {
  method: "POST",
  headers: {
    Authorization: \`Bearer \${API_KEY}\`,
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  body: JSON.stringify({ confirmation_code: "YOUR_CONFIRMATION_CODE", notes: "" }),
}).then(r => r.json());
JS;

        $php = <<<PHP
<?php
\$key = '{$bearer}';
\$v1 = '{$apiV1}';
\$ch = curl_init(\$v1.'/transactions');
curl_setopt_array(\$ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer '.\$key,
        'Content-Type: application/json',
        'Accept: application/json',
    ],
    CURLOPT_POSTFIELDS => '{$createJson}',
    CURLOPT_RETURNTRANSFER => true,
]);
\$body = curl_exec(\$ch);
curl_close(\$ch);
echo \$body;
PHP;

        $py = <<<PY
import requests

API_KEY = "{$bearer}"
V1 = "{$apiV1}"

r = requests.get("{$apiRoot}/ping", timeout=30)
print(r.json())

r = requests.post(
    f"{V1}/transactions",
    headers={"Authorization": f"Bearer {API_KEY}", "Accept": "application/json"},
    json={
        "buyer_email": "buyer@example.com",
        "seller_email": "seller@example.com",
        "amount": 1500.5,
        "currency": "KES",
        "description": "API escrow",
        "terms": "Deliver on receipt of funds.",
    },
    timeout=30,
)
print(r.status_code, r.json())

r = requests.get(
    f"{V1}/transactions/{$sampleTxn}",
    headers={"Authorization": f"Bearer {API_KEY}", "Accept": "application/json"},
    timeout=30,
)
print(r.json())
PY;

        $ruby = <<<'RUBY'
require "net/http"
require "json"

API_KEY = "{{BEARER}}"
V1 = "{{V1}}"
uri = URI(V1 + "/transactions")
req = Net::HTTP::Post.new(uri)
req["Authorization"] = "Bearer " + API_KEY
req["Content-Type"] = "application/json"
req["Accept"] = "application/json"
req.body = {"buyer_email"=>"buyer@example.com","seller_email"=>"seller@example.com","amount"=>1500.5,"currency"=>"KES","description"=>"API escrow","terms"=>"Deliver on receipt of funds."}.to_json
res = Net::HTTP.start(uri.hostname, uri.port, use_ssl: uri.scheme == "https") { |h| h.request(req) }
puts res.body
RUBY;
        $ruby = str_replace(['{{BEARER}}', '{{V1}}'], [$bearer, $apiV1], $ruby);

        $go = <<<GO
package main

import (
	"bytes"
	"fmt"
	"net/http"
)

func main() {
	key := "{$bearer}"
	v1 := "{$apiV1}"
	body := []byte(`{$createJson}`)
	req, _ := http.NewRequest(http.MethodPost, v1+"/transactions", bytes.NewReader(body))
	req.Header.Set("Authorization", "Bearer "+key)
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")
	res, err := http.DefaultClient.Do(req)
	if err != nil { panic(err) }
	defer res.Body.Close()
	fmt.Println(res.Status)
}
GO;

        return [
            'curl' => implode("\n\n", [$curlPing, $curlCreate, $curlGet, $curlRelease]),
            'javascript' => $js,
            'php' => $php,
            'python' => $py,
            'ruby' => $ruby,
            'go' => $go,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function postmanCollectionArray(string $apiRoot, string $apiV1, string $apiKey): array
    {
        $authHeader = [
            ['key' => 'Authorization', 'value' => 'Bearer {{apiKey}}', 'type' => 'text'],
            ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text'],
        ];

        $jsonHeader = array_merge($authHeader, [
            ['key' => 'Content-Type', 'value' => 'application/json', 'type' => 'text'],
        ]);

        $item = function (string $name, string $method, string $path, array $headers = [], ?array $body = null): array {
            $req = [
                'method' => $method,
                'header' => $headers,
                'url' => '{{apiRoot}}'.$path,
            ];
            if ($body !== null) {
                $req['body'] = [
                    'mode' => 'raw',
                    'raw' => json_encode($body, JSON_UNESCAPED_SLASHES),
                ];
            }

            return [
                'name' => $name,
                'request' => $req,
            ];
        };

        $createBody = [
            'buyer_email' => 'buyer@example.com',
            'seller_email' => 'seller@example.com',
            'amount' => 1500.5,
            'currency' => 'KES',
            'description' => 'API escrow',
            'terms' => 'Deliver on receipt of funds.',
        ];

        return [
            'info' => [
                '_postman_id' => Str::uuid()->toString(),
                'name' => 'eConfirm Escrow API',
                'description' => 'Health check + Escrow v1. Set `apiRoot` (e.g. '.$apiRoot.') and `apiKey` (your ek_ key).',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'variable' => [
                ['key' => 'apiRoot', 'value' => $apiRoot],
                ['key' => 'apiKey', 'value' => $apiKey],
                ['key' => 'transactionId', 'value' => 'txn_xxxxxxxx'],
            ],
            'item' => [
                $item('GET /api/ping', 'GET', '/ping', []),
                [
                    'name' => 'Escrow v1',
                    'item' => [
                        $item('POST /api/v1/transactions', 'POST', '/v1/transactions', $jsonHeader, $createBody),
                        $item('GET /api/v1/transactions/{transaction_id}', 'GET', '/v1/transactions/{{transactionId}}', $authHeader),
                        $item('POST /api/v1/transactions/{transaction_id}/release', 'POST', '/v1/transactions/{{transactionId}}/release', $jsonHeader, [
                            'confirmation_code' => 'YOUR_CONFIRMATION_CODE',
                            'notes' => '',
                        ]),
                    ],
                ],
            ],
        ];
    }

    public function generateOrRegenerateKey(Request $request): RedirectResponse
    {
        $key = 'ek_'.Str::random(40);
        $request->user()->forceFill(['api_key' => $key])->save();

        return back()->with('new_api_key', $key);
    }
}
