<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::query()->where('email', $credentials['email'])->first();
        if (! $admin || ! Hash::check($credentials['password'], (string) $admin->password)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        if (! $admin->hasVerifiedEmail()) {
            try {
                $admin->sendEmailVerificationNotification();
            } catch (\Throwable) {
                // Mail may be misconfigured; still block login until verified.
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Please verify your email address before signing in. If mail is configured, we sent a new verification link.']);
        }

        if ($admin->twoFactorEnabled()) {
            $request->session()->regenerate();
            $request->session()->put('admin.two_factor_login', [
                'id' => $admin->id,
                'remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('admin.two-factor.show');
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();
        AdminActivityLogger::log('auth.login', null, null, ['email' => $credentials['email']]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
