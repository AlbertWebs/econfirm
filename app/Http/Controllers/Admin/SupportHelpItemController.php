<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportHelpItem;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportHelpItemController extends Controller
{
    public function index(Request $request): View
    {
        $kind = $request->string('kind')->toString();
        if ($kind !== '' && ! array_key_exists($kind, SupportHelpItem::kindLabels())) {
            $kind = '';
        }

        $query = SupportHelpItem::query()->ordered();
        if ($kind !== '') {
            $query->where('kind', $kind);
        }
        $items = $query->paginate(40)->withQueryString();

        return view('admin.support-help-items.index', compact('items', 'kind'));
    }

    public function create(Request $request): View
    {
        $kind = $request->string('kind')->toString();
        if (! array_key_exists($kind, SupportHelpItem::kindLabels())) {
            $kind = SupportHelpItem::KIND_QUICK_HELP;
        }
        $item = new SupportHelpItem(['kind' => $kind, 'is_published' => true, 'sort_order' => 0]);

        return view('admin.support-help-items.create', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $item = SupportHelpItem::create($data);
        AdminActivityLogger::log('support_help_item.created', SupportHelpItem::class, $item->id, ['kind' => $item->kind]);

        return redirect()->route('admin.support-help-items.index', ['kind' => $item->kind])->with('status', 'Item created.');
    }

    public function edit(SupportHelpItem $supportHelpItem): View
    {
        return view('admin.support-help-items.edit', ['item' => $supportHelpItem]);
    }

    public function update(Request $request, SupportHelpItem $supportHelpItem): RedirectResponse
    {
        $data = $this->validated($request);
        $supportHelpItem->update($data);
        AdminActivityLogger::log('support_help_item.updated', SupportHelpItem::class, $supportHelpItem->id, ['kind' => $supportHelpItem->kind]);

        return back()->with('status', 'Item saved.');
    }

    public function destroy(SupportHelpItem $supportHelpItem): RedirectResponse
    {
        $kind = $supportHelpItem->kind;
        $id = $supportHelpItem->id;
        $supportHelpItem->delete();
        AdminActivityLogger::log('support_help_item.deleted', SupportHelpItem::class, $id, ['kind' => $kind]);

        return redirect()->route('admin.support-help-items.index', ['kind' => $kind])->with('status', 'Item deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request): array
    {
        $validated = $request->validate([
            'kind' => ['required', 'in:'.implode(',', array_keys(SupportHelpItem::kindLabels()))],
            'title' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'icon' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'is_published' => ['boolean'],
        ]);
        $validated['is_published'] = $request->boolean('is_published');
        if ($validated['kind'] === SupportHelpItem::KIND_HELP_FAQ) {
            $validated['icon'] = null;
        }

        return $validated;
    }
}
