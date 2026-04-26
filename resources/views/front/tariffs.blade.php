@extends('front.master')

@section('seo_title', 'Tariffs & fee calculator — '.site_setting('site_name'))
@section('seo_description', 'Understand e-confirm platform commission and illustrative M-PESA B2C vs paybill/till (B2B-style) charges. Estimate what you may be billed when funding escrow.')
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
            Platform commission plus an illustrative M-PESA rail estimate for paying to a mobile wallet (B2C-style) or a paybill / till (B2B-style).
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
                Separately, moving funds on the M-PESA network may incur <strong>Safaricom / partner tariffs</strong>. Those differ by rail:
                paying to a <strong>phone number</strong> (often described as consumer / B2C-style) versus a <strong>paybill or till number</strong> (business / B2B-style).
            </p>
            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-5 text-sm text-amber-950">
                <p class="font-semibold text-amber-900 mb-2">Important</p>
                <p class="mb-0">
                    The M-PESA amounts below are <strong>illustrative tier estimates</strong> for planning. Actual charges depend on Safaricom’s published tariffs,
                    your product path, and partner pricing. Update <code class="text-xs bg-white/70 px-1 py-0.5 rounded">config/tariffs.php</code> (or env overrides) to match your live schedule.
                </p>
            </div>
        </div>

        <div
            id="tariff-calculator"
            class="rounded-2xl border-2 border-green-200 bg-gradient-to-br from-green-50 to-white p-6 sm:p-8 shadow-sm lg:sticky lg:top-24"
        >
            <script type="application/json" id="tariff-calculator-config">
@json([
    'commission_rate' => (float) ($tariffs['commission_rate'] ?? 0.01),
    'b2c_tiers' => $tariffs['mpesa']['b2c_tiers'] ?? [],
    'b2b_tiers' => $tariffs['mpesa']['b2b_tiers'] ?? [],
])
            </script>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Fee calculator</h2>
            <p class="text-sm text-gray-600 mb-6">Enter the escrow amount and a destination: Kenya mobile number or paybill / till.</p>

            <form class="space-y-4" id="tariff-calculator-form" novalidate>
                <div>
                    <label for="tc-amount" class="block text-sm font-medium text-gray-800 mb-1">Escrow amount (KES)</label>
                    <input type="number" min="1" step="1" id="tc-amount" name="amount" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="e.g. 25000">
                </div>
                <div>
                    <label for="tc-destination" class="block text-sm font-medium text-gray-800 mb-1">Phone or paybill / till</label>
                    <input type="text" id="tc-destination" name="destination" required autocomplete="off"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                           placeholder="0712…, +254712…, or 123456">
                    <p class="mt-1 text-xs text-gray-500">We detect paybills/tills as short numeric business numbers; otherwise we treat the value as a mobile wallet.</p>
                </div>
                <button type="submit" class="w-full rounded-xl bg-green-600 text-white font-semibold py-3 hover:bg-green-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 transition-colors">
                    Calculate
                </button>
            </form>

            <div id="tc-error" class="hidden mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"></div>

            <div id="tc-result" class="hidden mt-6 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm font-medium text-gray-600">Detected rail</span>
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
                <p class="text-xs text-gray-500" id="tc-destination-note"></p>
            </div>
        </div>
    </div>
</section>

<section class="py-14 bg-gray-50 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Reference tiers (KES)</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">B2C-style (phone)</h3>
                    <p class="text-xs text-gray-500 mt-1">Tier fee by principal band — illustrative.</p>
                </div>
                <div class="max-h-80 overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white sticky top-0 text-left text-gray-600 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-2 font-medium">From</th>
                                <th class="px-4 py-2 font-medium">To</th>
                                <th class="px-4 py-2 font-medium text-right">Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (($tariffs['mpesa']['b2c_tiers'] ?? []) as $row)
                                <tr>
                                    <td class="px-4 py-2 tabular-nums">{{ number_format((int) $row['min']) }}</td>
                                    <td class="px-4 py-2 tabular-nums">{{ number_format((int) $row['max']) }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums font-medium">{{ number_format((int) $row['fee']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">B2B-style (paybill / till)</h3>
                    <p class="text-xs text-gray-500 mt-1">Tier fee by principal band — illustrative.</p>
                </div>
                <div class="max-h-80 overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white sticky top-0 text-left text-gray-600 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-2 font-medium">From</th>
                                <th class="px-4 py-2 font-medium">To</th>
                                <th class="px-4 py-2 font-medium text-right">Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (($tariffs['mpesa']['b2b_tiers'] ?? []) as $row)
                                <tr>
                                    <td class="px-4 py-2 tabular-nums">{{ number_format((int) $row['min']) }}</td>
                                    <td class="px-4 py-2 tabular-nums">{{ number_format((int) $row['max']) }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums font-medium">{{ number_format((int) $row['fee']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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

    function digitsOnly(s) {
        return String(s || '').replace(/\D/g, '');
    }

    function normalizePhone254(raw) {
        var d = digitsOnly(raw);
        if (d.length === 12 && d.indexOf('254') === 0) return d;
        if (d.length === 10 && d.charAt(0) === '0' && (d.charAt(1) === '7' || d.charAt(1) === '1')) return '254' + d.slice(1);
        if (d.length === 9 && (d.charAt(0) === '7' || d.charAt(0) === '1')) return '254' + d;
        return '';
    }

    function classifyDestination(raw) {
        var trimmed = String(raw || '').trim();
        if (!trimmed) return { kind: 'unknown', label: '—', display: '' };

        var d = digitsOnly(trimmed);
        var p254 = normalizePhone254(trimmed);

        if (p254.length === 12) {
            return { kind: 'b2c', label: 'Phone (B2C-style)', display: '+' + p254 };
        }

        if (/^\d{5,7}$/.test(d) && d.length <= 7) {
            return { kind: 'b2b', label: 'Paybill / till (B2B-style)', display: d };
        }

        return { kind: 'unknown', label: 'Could not detect — enter 07… / +254… or a 5–7 digit paybill/till', display: trimmed };
    }

    var form = document.getElementById('tariff-calculator-form');
    var err = document.getElementById('tc-error');
    var res = document.getElementById('tc-result');

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
        var destEl = document.getElementById('tc-destination');
        var principal = Math.round(Number(amountEl.value));
        if (!isFinite(principal) || principal < 1) {
            showError('Enter a valid escrow amount in KES (whole shillings).');
            return;
        }

        var dest = classifyDestination(destEl.value);
        if (dest.kind === 'unknown') {
            showError(dest.label);
            return;
        }

        var tiers = dest.kind === 'b2c' ? b2cTiers : b2bTiers;
        var mpesaFee = tierFee(principal, tiers);
        if (mpesaFee === null) {
            showError('Amount is outside the configured illustrative tier tables. Adjust tiers in config/tariffs.php.');
            return;
        }

        var commission = Math.round(principal * commissionRate * 100) / 100;
        var total = Math.round(principal + commission + mpesaFee);

        document.getElementById('tc-rail-label').textContent = dest.label;
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

        var note = 'Destination interpreted as ' + dest.display + '. ';
        note += 'Total = principal + platform commission + illustrative M-PESA tier fee.';
        document.getElementById('tc-destination-note').textContent = note;

        res.classList.remove('hidden');
    });
})();
</script>
@endpush
@endsection
