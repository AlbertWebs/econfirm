<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\AdminActivityLogger;
use App\Support\LegalPageRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::query()->orderBy('slug')->paginate(30);

        return view('admin.pages.index', compact('pages'));
    }

    public function show(Page $page): View
    {
        return view('admin.pages.show', compact('page'));
    }

    public function create(Request $request): View
    {
        $page = new Page;

        if ($request->filled('slug')) {
            $page->slug = (string) $request->string('slug');
        }

        if ($request->filled('title')) {
            $page->title = (string) $request->string('title');
        }

        if ($request->filled('type')) {
            $page->type = (string) $request->string('type');
        } elseif ($page->slug && LegalPageRegistry::find((string) $page->slug) !== null) {
            $page->type = 'legal';
        }

        return view('admin.pages.create', compact('page'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePage($request, null);
        $page = Page::create($data);
        AdminActivityLogger::log('page.created', Page::class, $page->id, ['slug' => $page->slug]);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validatePage($request, (int) $page->id);
        $page->update($data);
        AdminActivityLogger::log('page.updated', Page::class, $page->id, ['slug' => $page->slug]);

        return back()->with('status', 'Page saved.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $slug = $page->slug;
        $id = $page->id;
        $page->delete();
        AdminActivityLogger::log('page.deleted', Page::class, $id, ['slug' => $slug]);

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePage(Request $request, ?int $pageId = null): array
    {
        $slugRule = Rule::unique('pages', 'slug');
        if ($pageId !== null) {
            $slugRule = $slugRule->ignore($pageId);
        }
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'title' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['boolean'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);
        $validated['is_published'] = $request->boolean('is_published');
        if (empty($validated['type'])) {
            $validated['type'] = null;
        }
        if (empty($validated['meta_description'])) {
            $validated['meta_description'] = null;
        }

        return $validated;
    }
}
