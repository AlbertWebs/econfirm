<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScamReport;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'status' => ['required', 'string', Rule::in(['pending', 'approved'])],
        ]);
        $scam_report->update($data);
        AdminActivityLogger::log('scam_report.status', ScamReport::class, $scam_report->id, ['status' => $data['status']]);

        return back()->with('status', 'Status updated.');
    }

    /**
     * Stream a submitted evidence file (images / PDFs / docs) to authenticated admins only.
     */
    public function evidence(ScamReport $scam_report, int $index): StreamedResponse
    {
        $paths = $scam_report->evidence;
        if (! is_array($paths) || ! isset($paths[$index])) {
            abort(404);
        }
        $path = $paths[$index];
        if (! is_string($path) || $path === '' || str_contains($path, '..')) {
            abort(404);
        }
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path, basename($path), [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
