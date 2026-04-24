<?php

use App\Models\Blog;

test('insights index page loads', function () {
    $this->get(route('insights.index'))
        ->assertOk()
        ->assertSee('Insights', false);
});

test('published blog appears on listing and show page', function () {
    $blog = Blog::query()->create([
        'title' => 'Test Insight Article',
        'slug' => 'test-insight-article',
        'excerpt' => 'Short excerpt for testing.',
        'content' => '<p>Hello world</p>',
        'author' => 'Admin',
        'status' => Blog::STATUS_PUBLISHED,
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('insights.index'))
        ->assertOk()
        ->assertSee('Test Insight Article', false);

    $this->get(route('insights.show', $blog->slug))
        ->assertOk()
        ->assertSee('Test Insight Article', false)
        ->assertSee('Hello world', false);

    $blog->forceDelete();
});

test('draft blog is not visible publicly', function () {
    $blog = Blog::query()->create([
        'title' => 'Draft Only',
        'slug' => 'draft-only-test',
        'excerpt' => null,
        'content' => '<p>Secret</p>',
        'author' => 'Admin',
        'status' => Blog::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $this->get(route('insights.index'))->assertDontSee('Draft Only', false);
    $this->get(route('insights.show', $blog->slug))->assertNotFound();

    $blog->forceDelete();
});
