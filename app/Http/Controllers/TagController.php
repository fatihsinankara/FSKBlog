<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Support\BlogContentCache;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function show(string $slug): Response
    {
        $page = request()->integer('page', 1);
        $cache = app(BlogContentCache::class);

        $data = $cache->remember('tags.show', [$slug, 'page', $page], 300, function () use ($slug) {
            $tag = Tag::query()
                ->where('slug', $slug)
                ->firstOrFail();

            $posts = Post::published()
                ->forTag($slug)
                ->select(BlogContentCache::POST_LISTING_COLUMNS)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->paginate(9)
                ->through(fn (Post $post) => $post->toArray());

            return [
                'tag' => $tag->toArray(),
                'posts' => $posts->toArray(),
            ];
        });

        return Inertia::render('Blog/TagShow', [
            'tag'   => $data['tag'],
            'posts' => $data['posts'],
        ]);
    }
}
