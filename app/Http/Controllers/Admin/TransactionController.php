<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MpesaStkPush;
use App\Models\SmsLog;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $transactions = (clone $query)
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $statuses = Transaction::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        return view('admin.transactions.index', compact('transactions', 'statuses'));
    }

    public function show(Transaction $transaction)
    {
        $stkRows = collect();
        if (! empty($transaction->checkout_request_id)) {
            $stkRows = MpesaStkPush::query()
                ->where('checkout_request_id', $transaction->checkout_request_id)
                ->orderByDesc('id')
                ->get();
        }
        if ($stkRows->isEmpty() && ! empty($transaction->transaction_id)) {
            $stkRows = MpesaStkPush::query()
                ->where('reference', $transaction->transaction_id)
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.transactions.show', compact('transaction', 'stkRows'));
    }

    public function destroy(Transaction $transaction)
    {
        $transactionId = $transaction->transaction_id;

        try {
            DB::transaction(function () use ($transaction, $transactionId) {
                if (Schema::hasTable('sms_logs')) {
                    SmsLog::query()->where('correlator', 'like', $transactionId.'%')->delete();
                }

                // Explicitly clean up chat/dispute descendants for DBs missing full FK cascades.
                if (Schema::hasTable('live_chats')) {
                    $chatIds = DB::table('live_chats')
                        ->where('transaction_id', $transaction->id)
                        ->pluck('id');

                    if ($chatIds->isNotEmpty()) {
                        if (Schema::hasTable('live_chat_messages')) {
                            DB::table('live_chat_messages')->whereIn('live_chat_id', $chatIds)->delete();
                        }
                        if (Schema::hasTable('disputes')) {
                            DB::table('disputes')->whereIn('live_chat_id', $chatIds)->delete();
                        }
                    }
                }

                if (Schema::hasTable('disputes')) {
                    DB::table('disputes')->where('transaction_id', $transaction->id)->delete();
                }
                if (Schema::hasTable('live_chats')) {
                    DB::table('live_chats')->where('transaction_id', $transaction->id)->delete();
                }

                $transaction->delete();
            });
        } catch (QueryException $e) {
            Log::warning('Transaction delete blocked by DB constraint', [
                'transaction_id' => $transactionId,
                'transaction_pk' => $transaction->id,
                'sql_state' => $e->getCode(),
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.transactions.index')
                ->with('error', 'Could not delete transaction '.$transactionId.'. Some related records are still protected.');
        }

        return redirect()
            ->route('admin.transactions.index')
            ->with('status', 'Transaction '.$transactionId.' deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filteredQuery($request);

        $filename = 'transactions-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'id',
                'transaction_id',
                'status',
                'transaction_type',
                'amount',
                'sender_mobile',
                'receiver_mobile',
                'payment_method',
                'created_at',
                'updated_at',
            ]);
            $query->orderByDesc('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $t) {
                    fputcsv($out, [
                        $t->id,
                        $t->transaction_id,
                        $t->status,
                        $t->transaction_type,
                        $t->transaction_amount,
                        $t->sender_mobile,
                        $t->receiver_mobile,
                        $t->payment_method,
                        optional($t->created_at)->toDateTimeString(),
                        optional($t->updated_at)->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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

        if ($request->filled('min_amount')) {
            $q->where('transaction_amount', '>=', (float) $request->input('min_amount'));
        }
        if ($request->filled('max_amount')) {
            $q->where('transaction_amount', '<=', (float) $request->input('max_amount'));
        }

        return $q;
    }
}
