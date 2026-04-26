@extends('front.master')

@section('seo_title', 'Tariffs & fee calculator — '.site_setting('site_name'))
@section('seo_description', 'Understand e-confirm platform commission and illustrative M-PESA charges for phone number to phone number payouts versus phone number to till (paybill or till). Estimate what you may be billed when funding escrow.')
@section('canonical_url', route('tariffs.index'))

@section('content')
<section class="relative py-16 lg:py-20 bg-gradient-to-br from-green-50 via-white to-blue-50 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl mx-auto">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-6">
            <i class="fas fa-receipt text-3xl text-green-600" aria-hidden="true"></i>
        </div>
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">Tariffs &amp; charges</h1>
        <p class="text-lg sm:text-xl text-gray-600">
            Platform commission plus an illustrative M-PESA fee estimate—whether money moves <strong>phone number to phone number</strong> or <strong>phone number to till</strong> (paybill or till).
        </p>
    </div>
</section>

<section class="py-14 lg:py-18 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">
        <div class="space-y-6 text-gray-700 leading-relaxed">
            <h2 class="text-2xl font-bold text-gray-900">How fees fit together</h2>
            <p>
                When you fund escrow via STK Push, the amount charged to your M-PESA is typically your <strong>escrow principal</strong> plus the
                <strong>e-confirm platform commission</strong> (currently <strong>{{ number_format((float) ($tariffs['commission_rate'] ?? 0.01) * 100, 0) }}%</strong> of the principal, consistent with transaction creation in the app).
            </p>
            <p>
                Separately, moving funds on the M-PESA network may incur <strong>Safaricom / partner tariffs</strong>. The amount depends on how you pay out:
                <strong>phone number to phone number</strong> (another M-PESA line) versus <strong>phone number to till</strong> (paybill or till number).
            </p>
            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-5 text-sm text-amber-950">
                <p class="font-semibold text-amber-900 mb-2">Important</p>
                <p class="mb-0">
                    The M-PESA amounts below are <strong>illustrative tier estimates</strong> for planning. Actual charges depend on Safaricom’s published tariffs,
                    your product path, and partner pricing.
                </p>
            </div>
        </div>

        <div
            id="tariff-calculator"
            class="rounded-2xl border-2 border-green-200 bg-gradient-to-br from-green-50 to-white p-6 sm:p-8 shadow-sm lg:sticky lg:top-24"
            data-tariff-log-url="{{ route('tariffs.query.store') }}"
        >
            @php
                $tariffCalculatorConfig = [
                    'commission_rate' => (float) ($tariffs['commission_rate'] ?? 0.01),
                    'b2c_tiers' => $tariffs['mpesa']['b2c_tiers'] ?? [],
                    'b2b_tiers' => $tariffs['mpesa']['b2b_tiers'] ?? [],
                ];
            @endphp
            <script type="application/json" id="tariff-calculator-config">@json($tariffCalculatorConfig)</script>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Fee calculator</h2>
            <p class="text-sm text-gray-600 mb-6">Enter the escrow amount and choose whether the payout is <strong>phone number to phone number</strong> or <strong>phone number to till</strong>. The illustrative M-PESA fee uses the matching tier table.</p>

            <form class="space-y-4" id="tariff-calculator-form" novalidate>
                <div>
                    <label for="tc-amount" class="block text-sm font-medium text-gray-800 mb-1">Escrow amount (KES)</label>
                    <input type="number" min="1" step="1" id="tc-amount" name="amount" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="e.g. 25000">
                </div>
                <div>
                    <label for="tc-rail" class="block text-sm font-medium text-gray-800 mb-1">How the payout is sent</label>
                    <select id="tc-rail" name="rail" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500 bg-white">
                        <option value="b2c" selected>Phone number to phone number</option>
                        <option value="b2b">Phone number to till</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Fees shown are tier estimates by amount for the option you pick.</p>
                </div>
                <button type="submit" class="w-full rounded-xl bg-green-600 text-white font-semibold py-3 hover:bg-green-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 transition-colors">
                    Calculate
                </button>
            </form>

            <div id="tc-error" class="hidden mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"></div>

            <div id="tc-result" class="hidden mt-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm font-medium text-gray-600">How the payout is sent</span>
                    <span id="tc-rail-label" class="text-sm font-semibold text-gray-900"></span>
                </div>
                <dl class="divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white">
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-gray-600">Escrow principal</dt>
                        <dd class="font-medium text-gray-900 tabular-nums" id="tc-line-principal"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-gray-600">e-confirm commission (<span id="tc-commission-pct"></span>%)</dt>
                        <dd class="font-medium text-gray-900 tabular-nums" id="tc-line-commission"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm">
                        <dt class="text-gray-600">Illustrative M-PESA charge</dt>
                        <dd class="font-medium text-gray-900 tabular-nums" id="tc-line-mpesa"></dd>
                    </div>
                    <div class="flex justify-between gap-4 px-4 py-3 text-sm bg-green-50/80">
                        <dt class="font-semibold text-gray-900">Estimated total billed (STK)</dt>
                        <dd class="font-bold text-green-800 tabular-nums text-base" id="tc-line-total"></dd>
                    </div>
                </dl>
                <p class="text-xs text-gray-500" id="tc-footnote"></p>
            </div>
        </div>
    </div>
</section>

<section class="relative py-16 lg:py-20 bg-gradient-to-b from-gray-50 via-white to-green-50/30 border-t border-gray-200 overflow-hidden" aria-labelledby="tariff-tiers-heading">
    <div class="absolute inset-0 pointer-events-none opacity-40" aria-hidden="true">
        <div class="absolute top-20 right-0 w-72 h-72 bg-green-100 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10 lg:mb-12">
            <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-2">Planning reference</p>
            <h2 id="tariff-tiers-heading" class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">M-PESA tier estimates (KES)</h2>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                Principal bands and the illustrative fee we use in the calculator above. Same labels as in the form: <strong class="text-gray-800">phone number to phone number</strong> and <strong class="text-gray-800">phone number to till</strong>.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 lg:gap-8">
            {{-- Phone number to phone number (b2c) --}}
            <div class="group rounded-2xl border border-green-200/80 bg-white shadow-sm overflow-hidden ring-1 ring-green-900/5 hover:shadow-md hover:border-green-300/80 transition-shadow">
                <div class="flex items-start gap-4 px-5 sm:px-6 py-5 border-b border-green-100 bg-gradient-to-r from-green-50/90 to-white">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white shadow-sm" aria-hidden="true">
                        <i class="fas fa-mobile-screen text-lg"></i>
                    </span>
                    <div class="min-w-0 text-left">
                        <h3 class="text-lg font-bold text-gray-900">Phone number to phone number</h3>
                        <p class="text-sm text-gray-600 mt-1 leading-snug">Estimated M-PESA charge by escrow principal band.</p>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-80 sm:max-h-96 overflow-y-auto overscroll-contain">
                    <table class="min-w-full text-sm">
                        <caption class="sr-only">Phone number to phone number: principal from, principal to, fee in Kenyan shillings</caption>
                        <thead class="sticky top-0 z-10 bg-green-50/95 backdrop-blur-sm border-b border-green-100 text-left text-xs font-semibold uppercase tracking-wide text-green-900">
                            <tr>
                                <th scope="col" class="px-5 py-3 whitespace-nowrap">From (KES)</th>
                                <th scope="col" class="px-5 py-3 whitespace-nowrap">To (KES)</th>
                                <th scope="col" class="px-5 py-3 text-right whitespace-nowrap">Fee (KES)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse (($tariffs['mpesa']['b2c_tiers'] ?? []) as $row)
                                <tr class="hover:bg-green-50/40 transition-colors">
                                    <td class="px-5 py-2.5 tabular-nums text-gray-800">{{ number_format((int) $row['min']) }}</td>
                                    <td class="px-5 py-2.5 tabular-nums text-gray-800">{{ number_format((int) $row['max']) }}</td>
                                    <td class="px-5 py-2.5 text-right tabular-nums font-semibold text-green-800">{{ number_format((int) $row['fee']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">No tiers configured for this path.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="px-5 sm:px-6 py-3 text-xs text-gray-500 border-t border-gray-100 bg-gray-50/60">Illustrative only — not a quote from Safaricom.</p>
            </div>

            {{-- Phone number to till (b2b) --}}
            <div class="group rounded-2xl border border-emerald-200/80 bg-white shadow-sm overflow-hidden ring-1 ring-emerald-900/5 hover:shadow-md hover:border-emerald-300/80 transition-shadow">
                <div class="flex items-start gap-4 px-5 sm:px-6 py-5 border-b border-emerald-100 bg-gradient-to-r from-emerald-50/90 to-white">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm" aria-hidden="true">
                        <i class="fas fa-store text-lg"></i>
                    </span>
                    <div class="min-w-0 text-left">
                        <h3 class="text-lg font-bold text-gray-900">Phone number to till</h3>
                        <p class="text-sm text-gray-600 mt-1 leading-snug">Paybill or till: estimated charge by principal band.</p>
                    </div>
                </div>
                <div class="overflow-x-auto max-h-80 sm:max-h-96 overflow-y-auto overscroll-contain">
                    <table class="min-w-full text-sm">
                        <caption class="sr-only">Phone number to till: principal from, principal to, fee in Kenyan shillings</caption>
                        <thead class="sticky top-0 z-10 bg-emerald-50/95 backdrop-blur-sm border-b border-emerald-100 text-left text-xs font-semibold uppercase tracking-wide text-emerald-900">
                            <tr>
                                <th scope="col" class="px-5 py-3 whitespace-nowrap">From (KES)</th>
                                <th scope="col" class="px-5 py-3 whitespace-nowrap">To (KES)</th>
                                <th scope="col" class="px-5 py-3 text-right whitespace-nowrap">Fee (KES)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse (($tariffs['mpesa']['b2b_tiers'] ?? []) as $row)
                                <tr class="hover:bg-emerald-50/40 transition-colors">
                                    <td class="px-5 py-2.5 tabular-nums text-gray-800">{{ number_format((int) $row['min']) }}</td>
                                    <td class="px-5 py-2.5 tabular-nums text-gray-800">{{ number_format((int) $row['max']) }}</td>
                                    <td class="px-5 py-2.5 text-right tabular-nums font-semibold text-emerald-800">{{ number_format((int) $row['fee']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-500">No tiers configured for this path.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="px-5 sm:px-6 py-3 text-xs text-gray-500 border-t border-gray-100 bg-gray-50/60">Illustrative only — not a quote from Safaricom.</p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    var root = document.getElementById('tariff-calculator');
    if (!root) return;

    var cfgEl = document.getElementById('tariff-calculator-config');
    var cfg = {};
    try {
        cfg = cfgEl ? JSON.parse(cfgEl.textContent || '{}') : {};
    } catch (e) {
        cfg = {};
    }

    var commissionRate = parseFloat(cfg.commission_rate);
    if (!isFinite(commissionRate) || commissionRate < 0) commissionRate = 0.01;

    var b2cTiers = Array.isArray(cfg.b2c_tiers) ? cfg.b2c_tiers : [];
    var b2bTiers = Array.isArray(cfg.b2b_tiers) ? cfg.b2b_tiers : [];

    function formatKes(n) {
        var x = Math.round(Number(n) || 0);
        return x.toLocaleString('en-KE', { maximumFractionDigits: 0 });
    }

    function tierFee(amount, tiers) {
        var a = Math.round(Number(amount) || 0);
        for (var i = 0; i < tiers.length; i++) {
            var t = tiers[i];
            var lo = Number(t.min), hi = Number(t.max);
            if (a >= lo && a <= hi) return Math.round(Number(t.fee) || 0);
        }
        return null;
    }

    function railLabel(rail) {
        return rail === 'b2b' ? 'Phone number to till' : 'Phone number to phone number';
    }

    function logTariffSubmission(principal, rail) {
        var url = root.getAttribute('data-tariff-log-url');
        if (!url || !isFinite(principal) || principal < 1) return;
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ amount: principal, rail: rail })
        }).catch(function () {});
    }

    var railEl = document.getElementById('tc-rail');

    var form = document.getElementById('tariff-calculator-form');
    var err = document.getElementById('tc-error');
    var res = document.getElementById('tc-result');
    if (!form) return;

    function showError(msg) {
        err.textContent = msg;
        err.classList.remove('hidden');
        res.classList.add('hidden');
    }

    function clearError() {
        err.textContent = '';
        err.classList.add('hidden');
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearError();

        var amountEl = document.getElementById('tc-amount');
        var rail = railEl && railEl.value === 'b2b' ? 'b2b' : 'b2c';
        var principal = Math.round(Number(amountEl.value));
        if (!isFinite(principal) || principal < 1) {
            showError('Enter a valid escrow amount in KES (whole shillings).');
            return;
        }

        var tiers = rail === 'b2c' ? b2cTiers : b2bTiers;
        var mpesaFee = tierFee(principal, tiers);
        if (mpesaFee === null) {
            logTariffSubmission(principal, rail);
            showError('Amount is outside the configured illustrative tier tables. Adjust tiers in config/tariffs.php.');
            return;
        }

        var commission = Math.round(principal * commissionRate * 100) / 100;
        var total = Math.round(principal + commission + mpesaFee);

        document.getElementById('tc-rail-label').textContent = railLabel(rail);
        var pctDisplay = commissionRate * 100;
        if (Math.abs(pctDisplay - Math.round(pctDisplay)) < 1e-9) {
            pctDisplay = Math.round(pctDisplay);
        } else {
            pctDisplay = Math.round(pctDisplay * 100) / 100;
        }
        document.getElementById('tc-commission-pct').textContent = String(pctDisplay);
        document.getElementById('tc-line-principal').textContent = formatKes(principal) + ' KES';
        document.getElementById('tc-line-commission').textContent = formatKes(commission) + ' KES';
        document.getElementById('tc-line-mpesa').textContent = formatKes(mpesaFee) + ' KES';
        document.getElementById('tc-line-total').textContent = formatKes(total) + ' KES';

        document.getElementById('tc-footnote').textContent =
            'Total = principal + platform commission + illustrative M-PESA tier fee for the phone number to phone number or phone number to till option you selected.';

        res.classList.remove('hidden');
        logTariffSubmission(principal, rail);
    });
})();
</script>
@endpush
@endsection
