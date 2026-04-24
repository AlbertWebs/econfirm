<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiAccessController extends Controller
{
    public function index(Request $request): View
    {
        $q = User::query();
        if (! $request->boolean('show_all')) {
            $q->whereNotNull('api_key');
        }
        if ($request->filled('q')) {
            $needle = '%'.$request->string('q')->trim().'%';
            $q->where(function ($w) use ($needle) {
                $w->where('name', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('phone', 'like', $needle);
            });
        }
        $users = $q->orderByDesc('id')->paginate(25)->withQueryString();
        $apiV1 = econfirm_api_v1_url();
        $apiRoot = econfirm_api_root_url();
        $docsUrl = url('/api/documentation');
        $totalApiTx = Transaction::query()->whereNotNull('api_user_id')->count();

        return view('admin.api-access.index', compact('users', 'apiV1', 'apiRoot', 'docsUrl', 'totalApiTx'));
    }

    public function regenerateKey(User $user): RedirectResponse
    {
        $key = 'ek_'.Str::random(40);
        $user->forceFill(['api_key' => $key])->save();

        return back()->with('status', "New key for ".($user->email ?? ('#'.$user->id)).": {$key} — copy now; the previous key is no longer valid.");
    }
}
