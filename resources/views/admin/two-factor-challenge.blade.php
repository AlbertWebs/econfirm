<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-factor authentication — e-confirm admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/js/admin.js'])
</head>
<body class="flex min-h-full flex-col items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 px-4 py-12 font-sans antialiased">
    <div class="w-full max-w-md rounded-2xl border border-white/10 bg-white/95 p-8 shadow-2xl shadow-slate-900/40 backdrop-blur sm:p-10">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-900/30">
                <x-admin.icon name="shield" class="h-7 w-7 text-white" />
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Two-factor code</h1>
            <p class="mt-1 text-sm text-slate-500">Enter the 6-digit code from your authenticator app.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.two-factor.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="two-factor-code" class="mb-1.5 block text-sm font-medium text-slate-700">Authentication code</label>
                <input
                    type="text"
                    name="code"
                    id="two-factor-code"
                    value="{{ old('code') }}"
                    required
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    autofocus
                    class="block w-full rounded-lg border-slate-300 font-mono text-lg tracking-[0.3em] shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="000000"
                >
            </div>
            <button
                type="submit"
                class="flex w-full justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                Continue
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('admin.login') }}" class="font-medium text-emerald-700 hover:text-emerald-800">Back to sign in</a>
        </p>
    </div>
</body>
</html>
