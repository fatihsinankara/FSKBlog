<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $cacheKey = 'posts.index.'.request()->query('page', 1);

        $data = Cache::remember($cacheKey, 300, function () {
            $featured = Post::published()->featured()
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->first();

            $posts = Post::published()
                ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'category_id', 'status', 'published_at', 'reading_time'])
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
                ->paginate(9);

            return ['featured' => $featured, 'posts' => $posts];
        });

        return Inertia::render('Blog/Index', $data);
    }

    public function show(string $slug): Response
    {
        $post = Cache::remember("post.show.{$slug}", 600, function () use ($slug) {
            return Post::published()
                ->where('slug', $slug)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug', 'user:id,name'])
                ->firstOrFail();
        });

        $post->append('rendered_body');

        if (! session()->has('viewed_post_'.$post->id)) {
            $post->increment('views');
            session()->put('viewed_post_'.$post->id, true);
        }

        $comments = $post->comments()
            ->approved()
            ->with(['user:id,name'])
            ->latest()
            ->paginate(20, ['*'], 'comments_page')
            ->withQueryString();

        $related = Post::published()
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->where('id', '!=', $post->id)
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'category_id', 'published_at'])
            ->with(['category:id,name,slug,color'])
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post' => array_merge($post->toArray(), [
                'comments' => $comments,
            ]),
            'related' => $related,
        ]);
    }

    public function search(): Response
    {
        $term = request()->input('q', '');

        $posts = $term
            ? Post::published()->search($term)
                ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'category_id', 'status', 'published_at', 'reading_time'])
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->paginate(9)
                ->withQueryString()
            : Post::published()
                ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'category_id', 'status', 'published_at', 'reading_time'])
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString();

        return Inertia::render('Blog/Search', [
            'posts' => $posts,
            'query' => $term,
        ]);
    }
}
