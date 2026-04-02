<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $featured = Post::published()->featured()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->first();

        $posts = Post::published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->paginate(9);

        return Inertia::render('Blog/Index', [
            'featured' => $featured,
            'posts'    => $posts,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['category', 'tags', 'user', 'comments' => fn ($q) => $q->approved()->latest()])
            ->firstOrFail();

        $post->increment('views');

        $related = Post::published()
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->where('id', '!=', $post->id)
            ->with(['category'])
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('Blog/Show', [
            'post'    => $post,
            'related' => $related,
        ]);
    }

    public function search(): Response
    {
        $term = request()->input('q', '');

        $posts = $term
            ? Post::published()->search($term)->with(['category', 'tags'])->paginate(9)->withQueryString()
            : Post::published()->with(['category', 'tags'])->latest('published_at')->paginate(9)->withQueryString();

        return Inertia::render('Blog/Search', [
            'posts' => $posts,
            'query' => $term,
        ]);
    }
}
