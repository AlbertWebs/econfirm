<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AdminActivityLogger;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class AdminSecurityController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Admin $admin */
        $admin = $request->user('admin');

        $qrSvg = null;
        $otpauthUrl = null;
        if ($admin->two_factor_secret && ! $admin->two_factor_confirmed_at) {
            $google2fa = new Google2FA;
            $otpauthUrl = $google2fa->getQRCodeUrl(
                (string) config('app.name'),
                (string) $admin->email,
                $admin->two_factor_secret,
            );
            $options = new QROptions([
                'outputInterface' => QRMarkupSVG::class,
                'eccLevel' => EccLevel::M,
                'svgAddXmlHeader' => false,
                'scale' => 4,
            ]);
            $qrSvg = (new QRCode($options))->render($otpauthUrl);
        }

        return view('admin.security.index', [
            'admin' => $admin,
            'qrSvg' => $qrSvg,
            'otpauthUrl' => $otpauthUrl,
        ]);
    }

    public function sendVerificationEmail(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = $request->user('admin');
        if ($admin->hasVerifiedEmail()) {
            return back()->with('status', 'This email is already verified.');
        }

        $admin->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent to your inbox.');
    }

    public function startTwoFactor(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = $request->user('admin');
        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();

        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        AdminActivityLogger::log('security.two_factor.start', Admin::class, $admin->id);

        return redirect()->route('admin.security.index')
            ->with('status', 'Scan the QR code with your authenticator app, then enter a 6-digit code to confirm.');
    }

    public function confirmTwoFactor(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = $request->user('admin');
        $request->validate([
            'code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
        ]);

        if (! $admin->two_factor_secret || $admin->two_factor_confirmed_at) {
            return back()->with('error', 'Two-factor setup is not pending.');
        }

        $google2fa = new Google2FA;
        if (! $google2fa->verifyKey($admin->two_factor_secret, $request->string('code')->toString(), 1)) {
            return back()->withErrors(['code' => 'Invalid code. Try again.']);
        }

        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::lower(Str::random(10));
        }

        $admin->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $codes,
        ])->save();

        AdminActivityLogger::log('security.two_factor.enabled', Admin::class, $admin->id);

        return redirect()->route('admin.security.index')
            ->with('recovery_codes', $codes)
            ->with('status', 'Two-factor authentication is on. Store your recovery codes in a safe place.');
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        /** @var Admin $admin */
        $admin = $request->user('admin');
        if (! Hash::check($request->string('password')->toString(), (string) $admin->password)) {
            return back()->withErrors(['password' => 'The password is incorrect.']);
        }

        $admin->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        AdminActivityLogger::log('security.two_factor.disabled', Admin::class, $admin->id);

        return redirect()->route('admin.security.index')
            ->with('status', 'Two-factor authentication has been turned off.');
    }
}
