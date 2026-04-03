<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\UserContentFollow;
use App\Support\BlogContentCache;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $cache = app(BlogContentCache::class);

        $categories = $cache->remember('categories.index', [], 300, fn () => Category::query()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => $category->toArray())
            ->all());

        return Inertia::render('Blog/Categories', [
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): Response
    {
        $page = request()->integer('page', 1);
        $cache = app(BlogContentCache::class);

        $data = $cache->remember('categories.show', [$slug, 'page', $page], 300, function () use ($slug) {
            $category = Category::query()
                ->where('slug', $slug)
                ->firstOrFail();

            $posts = Post::published()
                ->forCategory($slug)
                ->select(BlogContentCache::POST_LISTING_COLUMNS)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->paginate(9)
                ->through(fn (Post $post) => $post->toArray());

            return [
                'category' => $category->toArray(),
                'posts' => $posts->toArray(),
            ];
        });

        return Inertia::render('Blog/CategoryShow', [
            'category' => $data['category'],
            'posts' => $data['posts'],
            'is_following' => auth()->check()
                ? UserContentFollow::query()
                    ->where('user_id', auth()->id())
                    ->where('followable_type', Category::class)
                    ->where('followable_id', $data['category']['id'])
                    ->exists()
                : false,
        ]);
    }
}
