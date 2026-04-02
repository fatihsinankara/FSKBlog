<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function show(string $slug): Response
    {
        $cacheKey = sprintf(
            'tags.show.payload.v%s.%s.page.%s',
            Post::contentCacheVersion(),
            $slug,
            request()->integer('page', 1)
        );

        $data = Cache::remember($cacheKey, 300, function () use ($slug) {
            $tag = Tag::query()
                ->where('slug', $slug)
                ->firstOrFail();

            $posts = Post::published()
                ->forTag($slug)
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
