@extends('layouts.admin')

@section('title', 'Security')
@section('page_title', 'Account security')

@section('content')
    <div class="mb-6 max-w-3xl">
        <p class="text-sm text-slate-600">Verify your email and protect the admin panel with two-factor authentication (TOTP).</p>
    </div>

    @if (session('recovery_codes'))
        <div class="mb-8 max-w-3xl rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-950">
            <p class="text-sm font-semibold">Save these recovery codes now</p>
            <p class="mt-1 text-sm text-amber-900/90">Each code can be used once if you lose your device. They will not be shown again.</p>
            <ul class="mt-3 grid gap-1 font-mono text-sm sm:grid-cols-2">
                @foreach (session('recovery_codes') as $code)
                    <li class="rounded bg-white/80 px-2 py-1">{{ $code }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8 max-w-3xl space-y-8">
        <x-admin.card>
            <x-slot name="header">Email verification</x-slot>
            @if ($admin->hasVerifiedEmail())
                <p class="text-sm text-emerald-800">Your address <span class="font-medium">{{ $admin->email }}</span> is verified.</p>
            @else
                <p class="mb-4 text-sm text-slate-600">You must verify this email before you can use the admin panel.</p>
                <form method="post" action="{{ route('admin.security.email.resend') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        Send verification link
                    </button>
                </form>
            @endif
        </x-admin.card>

        <x-admin.card>
            <x-slot name="header">Two-factor authentication (TOTP)</x-slot>
            @if ($admin->twoFactorEnabled())
                <p class="mb-4 text-sm text-slate-600">Two-factor is <strong class="text-emerald-700">on</strong>. Sign-in requires your authenticator app.</p>
                <form method="post" action="{{ route('admin.security.two-factor.disable') }}" class="max-w-md space-y-4">
                    @csrf
                    <div>
                        <label for="disable-2fa-password" class="mb-1.5 block text-sm font-medium text-slate-700">Current password</label>
                        <input
                            type="password"
                            name="password"
                            id="disable-2fa-password"
                            required
                            class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            autocomplete="current-password"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-800 transition hover:bg-red-100">
                        Turn off two-factor
                    </button>
                </form>
            @elseif ($admin->two_factor_secret && ! $admin->two_factor_confirmed_at)
                <p class="mb-4 text-sm text-slate-600">Scan this QR in Google Authenticator, Authy, or another TOTP app, then enter a code to finish setup.</p>
                @if ($qrSvg)
                    <div class="mb-4 inline-block rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                        {!! $qrSvg !!}
                    </div>
                @endif
                @if ($otpauthUrl)
                    <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-500">Manual entry</p>
                    <code class="mb-6 block break-all rounded-lg bg-slate-100 px-3 py-2 text-xs text-slate-800">{{ $otpauthUrl }}</code>
                @endif
                <form method="post" action="{{ route('admin.security.two-factor.confirm') }}" class="max-w-xs space-y-4">
                    @csrf
                    <div>
                        <label for="confirm-2fa-code" class="mb-1.5 block text-sm font-medium text-slate-700">6-digit code</label>
                        <input
                            type="text"
                            name="code"
                            id="confirm-2fa-code"
                            required
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            class="block w-full rounded-lg border-slate-300 font-mono tracking-widest shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        Confirm and enable
                    </button>
                </form>
            @else
                <p class="mb-4 text-sm text-slate-600">Add a second step after your password using a time-based code from your phone.</p>
                <form method="post" action="{{ route('admin.security.two-factor.start') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        Set up two-factor
                    </button>
                </form>
            @endif
        </x-admin.card>
    </div>
@endsection
