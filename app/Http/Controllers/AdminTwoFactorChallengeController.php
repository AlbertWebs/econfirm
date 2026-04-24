<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AdminTwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (! $request->session()->has('admin.two_factor_login')) {
            return redirect()->route('admin.login')
                ->with('error', 'Your authentication session expired. Please sign in again.');
        }

        return view('admin.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ]);

        $payload = $request->session()->get('admin.two_factor_login');
        if (! is_array($payload) || empty($payload['id'])) {
            return redirect()->route('admin.login')
                ->with('error', 'Your authentication session expired. Please sign in again.');
        }

        $admin = Admin::query()->find($payload['id']);
        if (! $admin || ! $admin->twoFactorEnabled()) {
            $request->session()->forget('admin.two_factor_login');

            return redirect()->route('admin.login')
                ->with('error', 'Two-factor authentication is no longer required for this account. Please sign in again.');
        }

        $secret = $admin->two_factor_secret;
        if (! is_string($secret) || $secret === '') {
            $request->session()->forget('admin.two_factor_login');

            return redirect()->route('admin.login')->with('error', 'Invalid two-factor configuration.');
        }

        $google2fa = new Google2FA;
        if (! $google2fa->verifyKey($secret, $request->string('code')->toString(), 1)) {
            return back()->withErrors(['code' => 'That code is invalid or has expired.'])->withInput();
        }

        $remember = (bool) ($payload['remember'] ?? false);
        $request->session()->forget('admin.two_factor_login');

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();
        AdminActivityLogger::log('auth.login', null, null, ['email' => $admin->email, 'two_factor' => true]);

        return redirect()->intended(route('admin.dashboard'));
    }
}
