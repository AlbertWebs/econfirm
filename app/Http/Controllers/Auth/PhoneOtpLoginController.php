<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class PhoneOtpLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a login OTP to the phone number if it belongs to a registered account.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $normalized = SmsService::normalizeKenyaTo254($validated['phone']);
        if (! preg_match('/^254\d{9}$/', $normalized)) {
            throw ValidationException::withMessages([
                'phone' => __('Enter a valid Kenya mobile number.'),
            ]);
        }

        $user = User::findByKenyaPhone($validated['phone']);
        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => __('No account is registered with this phone number. Use email login or contact support.'),
            ]);
        }

        try {
            $otp = Otp::createForPhone($normalized, 10);
        } catch (\Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'phone' => __('Could not create a verification code. Please try again.'),
            ]);
        }

        $sms = new SmsService();
        $message = "Your eConfirm login code is: {$otp->otp_code}. Valid for 10 minutes. Do not share this code.";
        $result = $sms->send($normalized, $message, 'login-otp-' . $user->id);

        if (! is_array($result) || ! SmsService::resultIndicatesSuccess($result)) {
            \Log::warning('Login OTP SMS not accepted', [
                'phone' => $normalized,
                'result' => $result,
            ]);

            throw ValidationException::withMessages([
                'phone' => __('We could not send an SMS right now. Please try again later or use email login.'),
            ]);
        }

        $request->session()->put('login_otp_phone', $normalized);
        $request->session()->put('login_otp_sent_at', now()->timestamp);

        return redirect()->route('login')->with('phone_otp_sent', true);
    }

    /**
     * Verify OTP and sign the user in (no password).
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $phone = $request->session()->get('login_otp_phone');
        if (! $phone || ! preg_match('/^254\d{9}$/', $phone)) {
            throw ValidationException::withMessages([
                'otp' => __('Send a verification code to your phone first.'),
            ]);
        }

        $sentAt = $request->session()->get('login_otp_sent_at');
        if ($sentAt && (now()->timestamp - $sentAt) > 15 * 60) {
            $request->session()->forget(['login_otp_phone', 'login_otp_sent_at']);

            throw ValidationException::withMessages([
                'otp' => __('The code expired. Request a new one.'),
            ]);
        }

        $otpRecord = Otp::verify($phone, $request->otp);
        if (! $otpRecord) {
            throw ValidationException::withMessages([
                'otp' => __('Invalid or expired code. Request a new code if needed.'),
            ]);
        }

        $user = User::findByKenyaPhone($phone);
        if (! $user) {
            $request->session()->forget(['login_otp_phone', 'login_otp_sent_at']);

            throw ValidationException::withMessages([
                'otp' => __('Account not found.'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->forget(['login_otp_phone', 'login_otp_sent_at']);
        $request->session()->regenerate();

        return $this->redirectAfterPhoneLogin($user);
    }

    /**
     * Clear phone OTP session state and return to the login form.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget(['login_otp_phone', 'login_otp_sent_at']);

        return redirect()->route('login');
    }

    protected function redirectAfterPhoneLogin(User $user): RedirectResponse
    {
        if ($user->type == '1') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        if ($user->type == '2') {
            if (Route::has('api.home')) {
                return redirect()->intended(route('api.home', absolute: false));
            }

            return redirect()->intended(route('user.dashboard', absolute: false));
        }

        return redirect()->intended(route('user.dashboard', absolute: false));
    }
}
