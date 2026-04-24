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
     * Append files to evidence (same rules as public report submission; stored under scam-reports/{id}).
     */
    public function appendEvidence(Request $request, ScamReport $scam_report): RedirectResponse
    {
        $existing = is_array($scam_report->evidence)
            ? array_values(array_filter($scam_report->evidence, static fn ($p) => is_string($p) && $p !== ''))
            : [];
        $existingCount = count($existing);
        $maxTotal = 20;
        $maxPerRequest = 5;
        $remaining = max(0, $maxTotal - $existingCount);

        if ($remaining === 0) {
            return back()->withErrors([
                'evidence_files' => "This report already has the maximum of {$maxTotal} evidence file(s).",
            ]);
        }

        $data = $request->validate([
            'evidence_files' => 'required|array|min:1|max:'.$maxPerRequest,
            'evidence_files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ], [
            'evidence_files.required' => 'Choose at least one file to upload.',
        ]);

        $files = (array) ($data['evidence_files'] ?? []);
        if (count($files) > $remaining) {
            return back()->withErrors([
                'evidence_files' => "You can add at most {$remaining} more file(s) (up to {$maxTotal} per report).",
            ]);
        }

        $newPaths = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $newPaths[] = $file->store("scam-reports/{$scam_report->id}", 'local');
            }
        }

        if ($newPaths === []) {
            return back()->withErrors(['evidence_files' => 'No valid files were uploaded.']);
        }

        $scam_report->update(['evidence' => array_merge($existing, $newPaths)]);
        AdminActivityLogger::log('scam_report.evidence', ScamReport::class, $scam_report->id, [
            'added' => count($newPaths),
        ]);

        $msg = count($newPaths) === 1
            ? '1 file uploaded.'
            : count($newPaths).' files uploaded.';

        return back()->with('status', $msg);
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
