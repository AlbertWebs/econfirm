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

        return view('admin.disputes.index', [
            'disputes' => $disputes,
            'statuses' => Dispute::STATUSES,
            'filterStatus' => $request->string('status')->toString(),
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
