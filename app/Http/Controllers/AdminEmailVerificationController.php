<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminEmailVerificationController extends Controller
{
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        $admin = Admin::query()->findOrFail($id);

        if (! hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if (! $admin->hasVerifiedEmail()) {
            $admin->markEmailAsVerified();
        }

        return redirect()->route('admin.login')
            ->with('status', 'Your email is verified. You can sign in now.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $admin = Admin::query()->where('email', $data['email'])->first();

        if ($admin && ! $admin->hasVerifiedEmail()) {
            try {
                $admin->sendEmailVerificationNotification();
            } catch (\Throwable) {
                // Avoid leaking whether the account exists if mail throws.
            }
        }

        return redirect()->route('admin.login')
            ->withInput(['email' => $data['email']])
            ->with('status', 'If that account exists and still needs verification, check your inbox for a new link.');
    }
}
