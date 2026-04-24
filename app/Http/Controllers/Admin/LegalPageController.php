<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\LegalPageRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function index(): View
    {
        $rows = [];
        foreach (LegalPageRegistry::entries() as $entry) {
            $page = Page::query()->where('slug', $entry['slug'])->first();
            $rows[] = [
                'definition' => $entry,
                'page' => $page,
            ];
        }

        return view('admin.legal-pages.index', ['rows' => $rows]);
    }

    /**
     * Open CMS editor for this legal slug (create or edit).
     */
    public function edit(string $slug): RedirectResponse
    {
        if (LegalPageRegistry::find($slug) === null) {
            abort(404);
        }

        $page = Page::query()->where('slug', $slug)->first();
        if ($page) {
            return redirect()->route('admin.pages.edit', $page);
        }

        $def = LegalPageRegistry::find($slug);

        return redirect()->route('admin.pages.create', [
            'slug' => $slug,
            'title' => $def['label'],
            'type' => 'legal',
        ]);
    }
}
