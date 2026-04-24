<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MpesaB2b;
use App\Models\MpesaB2c;
use App\Models\MpesaC2bTransaction;
use App\Models\MpesaStkPush;
use App\Services\AdminActivityLogger;
use App\Services\MpesaAdminApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class MpesaTransactionsController extends Controller
{
    /** @var list<string> */
    protected const TABS = ['stk', 'c2b', 'b2c', 'b2b'];

    public function index(Request $request): View
    {
        $tab = strtolower((string) $request->query('tab', 'stk'));
        if (! in_array($tab, self::TABS, true)) {
            $tab = 'stk';
        }

        $filters = [
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'status' => $request->query('status'),
            'phone' => $request->query('phone'),
            'transaction_id' => $request->query('transaction_id'),
        ];

        $rows = match ($tab) {
            'stk' => $this->paginateStk($request, $filters),
            'c2b' => $this->paginateC2b($request, $filters),
            'b2c' => $this->paginateB2c($request, $filters),
            'b2b' => $this->paginateB2b($request, $filters),
        };

        $summary = [
            'stk_total' => MpesaStkPush::query()->count(),
            'c2b_total_amount' => (float) MpesaC2bTransaction::query()->sum('amount'),
            'c2b_count' => MpesaC2bTransaction::query()->count(),
            'b2c_total_amount' => (float) MpesaB2c::query()->sum('amount'),
            'b2c_count' => MpesaB2c::query()->count(),
            'b2b_total_amount' => (float) MpesaB2b::query()->sum('amount'),
            'b2b_count' => MpesaB2b::query()->count(),
            'pending_approvals' => MpesaB2c::query()->whereRaw('LOWER(COALESCE(status, "")) = ?', ['pending'])->count()
                + MpesaB2b::query()->whereRaw('LOWER(COALESCE(status, "")) = ?', ['pending'])->count(),
        ];

        return view('admin.mpesa-transactions.index', compact('tab', 'rows', 'filters', 'summary'));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function paginateStk(Request $request, array $filters): LengthAwarePaginator
    {
        $q = MpesaStkPush::query()->orderByDesc('id');
        $this->applyDateRange($q, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'created_at');
        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['phone'])) {
            $q->where('phone', 'like', '%'.$filters['phone'].'%');
        }
        if (! empty($filters['transaction_id'])) {
            $t = trim((string) $filters['transaction_id']);
            $q->where(function ($w) use ($t) {
                $w->where('reference', 'like', '%'.$t.'%')
                    ->orWhere('checkout_request_id', 'like', '%'.$t.'%');
                if (ctype_digit($t)) {
                    $w->orWhere('id', (int) $t);
                }
            });
        }

        return $q->paginate(25)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function paginateC2b(Request $request, array $filters): LengthAwarePaginator
    {
        $q = MpesaC2bTransaction::query()->orderByDesc('id');
        $this->applyDateRange($q, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'created_at');
        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['phone'])) {
            $q->where('phone', 'like', '%'.$filters['phone'].'%');
        }
        if (! empty($filters['transaction_id'])) {
            $t = trim((string) $filters['transaction_id']);
            $q->where(function ($w) use ($t) {
                $w->where('transaction_id', 'like', '%'.$t.'%')
                    ->orWhere('bill_reference_number', 'like', '%'.$t.'%')
                    ->orWhere('mpesa_receipt_number', 'like', '%'.$t.'%');
                if (ctype_digit($t)) {
                    $w->orWhere('id', (int) $t);
                }
            });
        }

        return $q->paginate(25)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function paginateB2c(Request $request, array $filters): LengthAwarePaginator
    {
        $q = MpesaB2c::query()->with(['approvedByAdmin', 'rejectedByAdmin'])->orderByDesc('id');
        $this->applyDateRange($q, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'created_at');
        if (! empty($filters['status'])) {
            $q->whereRaw('LOWER(COALESCE(status, "")) = ?', [strtolower((string) $filters['status'])]);
        }
        if (! empty($filters['phone'])) {
            $p = '%'.$filters['phone'].'%';
            $q->where(function ($w) use ($p) {
                $w->where('receiver_mobile', 'like', $p)->orWhere('party_b', 'like', $p);
            });
        }
        if (! empty($filters['transaction_id'])) {
            $t = trim((string) $filters['transaction_id']);
            $q->where(function ($w) use ($t) {
                $w->where('transaction_id', 'like', '%'.$t.'%')
                    ->orWhere('conversation_id', 'like', '%'.$t.'%')
                    ->orWhere('originator_conversation_id', 'like', '%'.$t.'%');
                if (ctype_digit($t)) {
                    $w->orWhere('id', (int) $t);
                }
            });
        }

        return $q->paginate(25)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    protected function paginateB2b(Request $request, array $filters): LengthAwarePaginator
    {
        $q = MpesaB2b::query()->orderByDesc('id');
        $this->applyDateRange($q, $filters['date_from'] ?? null, $filters['date_to'] ?? null, 'created_at');
        if (! empty($filters['status'])) {
            $q->whereRaw('LOWER(COALESCE(status, "")) = ?', [strtolower((string) $filters['status'])]);
        }
        if (! empty($filters['phone'])) {
            $q->where(function ($w) use ($filters) {
                $p = '%'.$filters['phone'].'%';
                $w->where('party_a', 'like', $p)->orWhere('party_b', 'like', $p);
            });
        }
        if (! empty($filters['transaction_id'])) {
            $t = trim((string) $filters['transaction_id']);
            $q->where(function ($w) use ($t) {
                $w->where('transaction_id', 'like', '%'.$t.'%')
                    ->orWhere('remarks', 'like', '%'.$t.'%')
                    ->orWhere('conversation_id', 'like', '%'.$t.'%');
                if (ctype_digit($t)) {
                    $w->orWhere('id', (int) $t);
                }
            });
        }

        return $q->paginate(25)->withQueryString();
    }

    protected function applyDateRange($query, ?string $from, ?string $to, string $column): void
    {
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }
        if ($to) {
            $query->whereDate($column, '<=', $to);
        }
    }

    public function approveB2c(MpesaB2c $mpesa_b2c, MpesaAdminApprovalService $approval): RedirectResponse
    {
        $result = $approval->approveB2c($mpesa_b2c);
        if ($result['ok']) {
            AdminActivityLogger::log('mpesa.b2c.approved', MpesaB2c::class, $mpesa_b2c->id, []);
        }

        return redirect()
            ->route('admin.mpesa-transactions.index', array_merge(['tab' => 'b2c'], request()->only(['date_from', 'date_to', 'status', 'phone', 'transaction_id'])))
            ->with($result['ok'] ? 'status' : 'error', $result['message']);
    }

    public function rejectB2c(Request $request, MpesaB2c $mpesa_b2c, MpesaAdminApprovalService $approval): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);
        $result = $approval->rejectB2c($mpesa_b2c, $validated['rejection_reason']);
        if ($result['ok']) {
            AdminActivityLogger::log('mpesa.b2c.rejected', MpesaB2c::class, $mpesa_b2c->id, [
                'reason_chars' => strlen($validated['rejection_reason']),
            ]);
        }

        return redirect()
            ->route('admin.mpesa-transactions.index', array_merge(['tab' => 'b2c'], request()->only(['date_from', 'date_to', 'status', 'phone', 'transaction_id'])))
            ->with($result['ok'] ? 'status' : 'error', $result['message']);
    }

    public function approveB2b(MpesaB2b $mpesa_b2b, MpesaAdminApprovalService $approval): RedirectResponse
    {
        $result = $approval->approveB2b($mpesa_b2b);
        if ($result['ok']) {
            AdminActivityLogger::log('mpesa.b2b.approved', MpesaB2b::class, $mpesa_b2b->id, []);
        }

        return redirect()
            ->route('admin.mpesa-transactions.index', array_merge(['tab' => 'b2b'], request()->only(['date_from', 'date_to', 'status', 'phone', 'transaction_id'])))
            ->with($result['ok'] ? 'status' : 'error', $result['message']);
    }

    public function rejectB2b(Request $request, MpesaB2b $mpesa_b2b, MpesaAdminApprovalService $approval): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);
        $result = $approval->rejectB2b($mpesa_b2b, $validated['rejection_reason']);
        if ($result['ok']) {
            AdminActivityLogger::log('mpesa.b2b.rejected', MpesaB2b::class, $mpesa_b2b->id, [
                'reason_chars' => strlen($validated['rejection_reason']),
            ]);
        }

        return redirect()
            ->route('admin.mpesa-transactions.index', array_merge(['tab' => 'b2b'], request()->only(['date_from', 'date_to', 'status', 'phone', 'transaction_id'])))
            ->with($result['ok'] ? 'status' : 'error', $result['message']);
    }
}
