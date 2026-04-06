<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemaps = [
            ['loc' => route('sitemap.pages'),      'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.posts'),      'lastmod' => Post::published()->latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString()],
            ['loc' => route('sitemap.categories'), 'lastmod' => Category::latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString()],
            ['loc' => route('sitemap.tags'),       'lastmod' => Tag::latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString()],
        ];

        return response()
            ->view('sitemap.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    public function pages(): Response
    {
        $pages = array_merge([
            ['loc' => route('home'),              'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('categories.index'),  'priority' => '0.8', 'changefreq' => 'weekly'],
        ], Page::published()
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->get()
            ->map(fn (Page $page) => [
                'loc' => route('pages.show', $page->slug),
                'lastmod' => $page->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ])
            ->all());

        return response()
            ->view('sitemap.pages', compact('pages'))
            ->header('Content-Type', 'application/xml');
    }

    public function posts(): Response
    {
        $posts = Post::published()
            ->select('slug', 'updated_at')
            ->latest('updated_at')
            ->cursor();

        return response()
            ->view('sitemap.posts', compact('posts'))
            ->header('Content-Type', 'application/xml');
    }

    public function categories(): Response
    {
        $categories = Category::select('slug', 'updated_at')
            ->latest('updated_at')
            ->cursor();

        return response()
            ->view('sitemap.categories', compact('categories'))
            ->header('Content-Type', 'application/xml');
    }

    public function tags(): Response
    {
        $tags = Tag::select('slug', 'updated_at')
            ->latest('updated_at')
            ->cursor();

        return response()
            ->view('sitemap.tags', compact('tags'))
            ->header('Content-Type', 'application/xml');
    }
}
