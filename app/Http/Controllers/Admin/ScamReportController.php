<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScamReport;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScamReportController extends Controller
{
    public function index(Request $request)
    {
        $q = ScamReport::query();
        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($request->filled('q')) {
            $needle = '%'.$request->string('q')->trim().'%';
            $q->where(function ($w) use ($needle) {
                $w->where('description', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('phone', 'like', $needle);
            });
        }
        $reports = $q->orderByDesc('id')->paginate(30)->withQueryString();

        return view('admin.scam-reports.index', compact('reports'));
    }

    public function show(ScamReport $scam_report): View
    {
        return view('admin.scam-reports.show', ['report' => $scam_report]);
    }

    public function updateStatus(Request $request, ScamReport $scam_report): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]);
        $scam_report->update($data);
        AdminActivityLogger::log('scam_report.status', ScamReport::class, $scam_report->id, ['status' => $data['status']]);

        return back()->with('status', 'Status updated.');
    }
}
