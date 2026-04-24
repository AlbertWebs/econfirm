<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('dashboard.developer', [
            'apiV1Url' => $v1,
            'apiRootUrl' => $apiRoot,
            'keyPreview' => $keyPreview,
            'apiTransactions' => $apiTransactions,
        ]);
    }

    public function generateOrRegenerateKey(Request $request): RedirectResponse
    {
        $key = 'ek_'.Str::random(40);
        $request->user()->forceFill(['api_key' => $key])->save();

        return back()->with('new_api_key', $key);
    }
}
