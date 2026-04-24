<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsightsController extends Controller
{
    public function index(Request $request): View
    {
        $blogs = Blog::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('front.insights.index', compact('blogs'));
    }

    public function show(string $slug): View
    {
        $blog = Blog::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('front.insights.show', compact('blog'));
    }
}
