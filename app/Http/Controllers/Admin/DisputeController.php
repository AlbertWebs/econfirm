<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DisputeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Dispute::query()
            ->with(['transaction:id,transaction_id,status,transaction_amount', 'liveChat:id,status,updated_at']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $disputes = $query->orderByDesc('updated_at')->paginate(30)->withQueryString();

        $byStatus = Dispute::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.disputes.index', [
            'disputes' => $disputes,
            'statuses' => Dispute::STATUSES,
            'filterStatus' => $request->string('status')->toString(),
            'summary' => [
                'total' => Dispute::query()->count(),
                'created' => (int) ($byStatus[Dispute::STATUS_CREATED] ?? 0),
                'ongoing' => (int) ($byStatus[Dispute::STATUS_ONGOING] ?? 0),
                'resolved' => (int) ($byStatus[Dispute::STATUS_RESOLVED] ?? 0),
            ],
        ]);
    }

    public function updateStatus(Request $request, Dispute $dispute): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Dispute::STATUSES)],
        ]);

        $dispute->update(['status' => $validated['status']]);

        return redirect()->back()->with('status', 'Dispute status updated.');
    }
}
