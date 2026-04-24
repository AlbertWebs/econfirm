<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request);

        $summaryBase = clone $query;
        $summary = [
            'escrow_count' => (int) (clone $query)->count(),
            'volume_kes' => (float) (clone $query)->sum('transaction_amount'),
            'platform_fees_kes' => (float) (clone $query)->sum('transaction_fee'),
        ];

        $profitByStatus = (clone $summaryBase)
            ->select('status')
            ->selectRaw('COUNT(*) as escrow_count')
            ->selectRaw('COALESCE(SUM(transaction_fee), 0) as fee_total')
            ->selectRaw('COALESCE(SUM(transaction_amount), 0) as amount_total')
            ->groupBy('status')
            ->orderByDesc('fee_total')
            ->get();

        $profitByMonth = $this->profitByMonth(clone $summaryBase);

        $transactions = (clone $query)
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $statuses = Transaction::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        return view('admin.business.index', compact(
            'summary',
            'profitByStatus',
            'profitByMonth',
            'transactions',
            'statuses'
        ));
    }

    /**
     * @return Builder<Transaction>
     */
    protected function filteredQuery(Request $request): Builder
    {
        $q = Transaction::query();

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }

        if ($request->filled('q')) {
            $needle = '%'.$request->string('q')->trim().'%';
            $q->where(function ($w) use ($needle) {
                $w->where('transaction_id', 'like', $needle)
                    ->orWhere('sender_mobile', 'like', $needle)
                    ->orWhere('receiver_mobile', 'like', $needle);
            });
        }

        if ($request->filled('from_date')) {
            $q->whereDate('created_at', '>=', $request->string('from_date'));
        }
        if ($request->filled('to_date')) {
            $q->whereDate('created_at', '<=', $request->string('to_date'));
        }

        return $q;
    }

    /**
     * @return \Illuminate\Support\Collection<int, object{period: string, fee_total: float, escrow_count: int}>
     */
    protected function profitByMonth(Builder $base): \Illuminate\Support\Collection
    {
        $periodExpr = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        return (clone $base)
            ->selectRaw("$periodExpr as period")
            ->selectRaw('COUNT(*) as escrow_count')
            ->selectRaw('COALESCE(SUM(transaction_fee), 0) as fee_total')
            ->groupBy(DB::raw($periodExpr))
            ->orderBy(DB::raw($periodExpr))
            ->get();
    }
}
