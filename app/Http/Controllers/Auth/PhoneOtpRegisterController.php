<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\PhoneAccountProvisioningService;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PhoneOtpRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $normalized = SmsService::normalizeKenyaTo254($validated['phone']);
        if (! preg_match('/^254\d{9}$/', $normalized)) {
            throw ValidationException::withMessages([
                'phone' => __('Enter a valid Kenya mobile number.'),
            ]);
        }

        if (User::findByKenyaPhone($normalized)) {
            throw ValidationException::withMessages([
                'phone' => __('An account with this phone already exists. Please sign in.'),
            ]);
        }

        $otp = Otp::createForPhone($normalized, 10);
        $sms = new SmsService();
        $message = "Your eConfirm sign up code is: {$otp->otp_code}. Valid for 10 minutes. Do not share this code.";
        $result = $sms->send($normalized, $message, 'signup-otp-'.$normalized);

        if (! is_array($result) || ! SmsService::resultIndicatesSuccess($result)) {
            throw ValidationException::withMessages([
                'phone' => __('We could not send an SMS right now. Please try again.'),
            ]);
        }

        $request->session()->put('register_otp_phone', $normalized);
        $request->session()->put('register_otp_name', trim((string) $validated['name']));
        $request->session()->put('register_otp_sent_at', now()->timestamp);

        return redirect()->route('register')->with('register_otp_sent', true);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $phone = (string) $request->session()->get('register_otp_phone', '');
        $name = trim((string) $request->session()->get('register_otp_name', ''));
        $sentAt = (int) $request->session()->get('register_otp_sent_at', 0);

        if (! preg_match('/^254\d{9}$/', $phone) || $name === '') {
            throw ValidationException::withMessages([
                'otp' => __('Start sign up first by entering your name and phone.'),
            ]);
        }

        if ($sentAt > 0 && (now()->timestamp - $sentAt) > 15 * 60) {
            $request->session()->forget(['register_otp_phone', 'register_otp_name', 'register_otp_sent_at']);

            throw ValidationException::withMessages([
                'otp' => __('The code expired. Request a new one.'),
            ]);
        }

        if (! Otp::verify($phone, (string) $request->otp)) {
            throw ValidationException::withMessages([
                'otp' => __('Invalid or expired code. Request a new code if needed.'),
            ]);
        }

        if (User::findByKenyaPhone($phone)) {
            $request->session()->forget(['register_otp_phone', 'register_otp_name', 'register_otp_sent_at']);

            throw ValidationException::withMessages([
                'phone' => __('An account with this phone already exists. Please sign in.'),
            ]);
        }

        $user = PhoneAccountProvisioningService::ensureUser($phone, $name, true);
        if (! $user) {
            throw ValidationException::withMessages([
                'otp' => __('Could not create your account. Please try again.'),
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->forget(['register_otp_phone', 'register_otp_name', 'register_otp_sent_at']);
        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget(['register_otp_phone', 'register_otp_name', 'register_otp_sent_at']);

        return redirect()->route('register');
    }
}

