<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VelipayPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VelipayPaymentsController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request);

        $payments = (clone $query)
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $statuses = VelipayPayment::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        $summary = [
            'count' => (int) (clone $query)->count(),
            'amount' => (float) (clone $query)->sum('amount'),
            'paid_count' => (int) (clone $query)->whereIn('status', ['paid', 'settled', 'success'])->count(),
            'failed_count' => (int) (clone $query)->whereIn('status', ['failed', 'cancelled'])->count(),
        ];

        return view('admin.velipay-payments.index', compact('payments', 'statuses', 'summary'));
    }

    public function show(VelipayPayment $velipayPayment): View
    {
        return view('admin.velipay-payments.show', [
            'payment' => $velipayPayment,
        ]);
    }

    /**
     * @return Builder<VelipayPayment>
     */
    protected function filteredQuery(Request $request): Builder
    {
        $q = VelipayPayment::query();

        if ($request->filled('status')) {
            $q->where('status', (string) $request->input('status'));
        }

        if ($request->filled('from_date')) {
            $q->whereDate('created_at', '>=', (string) $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $q->whereDate('created_at', '<=', (string) $request->input('to_date'));
        }

        if ($request->filled('q')) {
            $needle = '%'.trim((string) $request->input('q')).'%';
            $q->where(function (Builder $w) use ($needle): void {
                $w->where('velipay_payment_id', 'like', $needle)
                    ->orWhere('transaction_id', 'like', $needle)
                    ->orWhere('merchant_reference', 'like', $needle)
                    ->orWhere('phone', 'like', $needle)
                    ->orWhere('receipt_number', 'like', $needle);
            });
        }

        return $q;
    }
}
