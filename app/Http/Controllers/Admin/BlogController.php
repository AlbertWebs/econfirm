<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\AdminActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $blogs = Blog::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('title', 'like', '%'.$q.'%')
                        ->orWhere('excerpt', 'like', '%'.$q.'%')
                        ->orWhere('slug', 'like', '%'.$q.'%');
                });
            })
            ->when(in_array($status, [Blog::STATUS_DRAFT, Blog::STATUS_PUBLISHED], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blogs.index', compact('blogs', 'q', 'status'));
    }

    public function create(): View
    {
        $blog = new Blog;
        $blog->author = 'Admin';
        $blog->status = Blog::STATUS_DRAFT;

        return view('admin.blogs.create', compact('blog'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request, null);
        $blog = Blog::create($data);
        AdminActivityLogger::log('blog.created', Blog::class, $blog->id, ['slug' => $blog->slug]);

        return redirect()->route('admin.blogs.edit', $blog)->with('status', 'Blog post created.');
    }

    public function edit(Blog $blog): View
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $data = $this->validatedData($request, $blog->id);
        $blog->update($data);
        AdminActivityLogger::log('blog.updated', Blog::class, $blog->id, ['slug' => $blog->slug]);

        return back()->with('status', 'Blog post saved.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $slug = $blog->slug;
        $id = $blog->id;
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        $blog->delete();
        AdminActivityLogger::log('blog.deleted', Blog::class, $id, ['slug' => $slug]);

        return redirect()->route('admin.blogs.index')->with('status', 'Blog post deleted.');
    }

    /**
     * TinyMCE image upload (JSON { location: url }).
     */
    public function uploadEditorImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('file')->store('blog/content', 'public');
        $url = Storage::disk('public')->url($path);

        return response()->json(['location' => $url]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedData(Request $request, ?int $blogId): array
    {
        $slugInput = trim((string) $request->input('slug'));
        $slugRules = ['nullable', 'string', 'max:255'];
        if ($slugInput !== '') {
            $slugUnique = Rule::unique('blogs', 'slug');
            if ($blogId !== null) {
                $slugUnique = $slugUnique->ignore($blogId);
            }
            $slugRules[] = $slugUnique;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'slug' => $slugRules,
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'author' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'action' => ['required', 'string', 'in:draft,publish'],
        ]);

        $slugInput = trim((string) ($validated['slug'] ?? ''));
        if ($slugInput === '') {
            $slug = Blog::uniqueSlugFromTitle($validated['title']);
        } else {
            $slug = Str::slug($slugInput);
        }

        $action = $validated['action'];
        $status = $action === 'publish' ? Blog::STATUS_PUBLISHED : Blog::STATUS_DRAFT;

        $publishedAt = null;
        if ($status === Blog::STATUS_PUBLISHED) {
            if ($blogId !== null) {
                $existing = Blog::query()->find($blogId);
                $publishedAt = $existing?->published_at ?? now();
            } else {
                $publishedAt = now();
            }
        }

        $author = trim((string) ($validated['author'] ?? ''));
        if ($author === '') {
            $author = 'Admin';
        }

        $data = [
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'author' => $author,
            'status' => $status,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'published_at' => $publishedAt,
        ];

        if ($request->boolean('remove_featured_image')) {
            if ($blogId !== null) {
                $blog = Blog::query()->find($blogId);
                if ($blog && $blog->featured_image) {
                    Storage::disk('public')->delete($blog->featured_image);
                }
            }
            $data['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            if ($blogId !== null) {
                $blog = Blog::query()->find($blogId);
                if ($blog && $blog->featured_image) {
                    Storage::disk('public')->delete($blog->featured_image);
                }
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog/featured', 'public');
        } elseif ($blogId === null) {
            $data['featured_image'] = null;
        }

        return $data;
    }
}
