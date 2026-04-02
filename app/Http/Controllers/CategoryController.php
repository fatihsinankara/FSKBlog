<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $cacheKey = sprintf('categories.index.payload.v%s', Post::contentCacheVersion());

        $categories = Cache::remember($cacheKey, 300, fn () => Category::query()
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
        $cacheKey = sprintf(
            'categories.show.payload.v%s.%s.page.%s',
            Post::contentCacheVersion(),
            $slug,
            request()->integer('page', 1)
        );

        $data = Cache::remember($cacheKey, 300, function () use ($slug) {
            $category = Category::query()
                ->where('slug', $slug)
                ->firstOrFail();

            $posts = Post::published()
                ->forCategory($slug)
                ->select([
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'featured_image',
                    'featured_image_alt',
                    'category_id',
                    'status',
                    'published_at',
                    'reading_time',
                ])
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
            'posts'    => $data['posts'],
        ]);
    }
}
