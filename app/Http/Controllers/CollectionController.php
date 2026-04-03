<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\UserContentFollow;
use App\Support\BlogContentCache;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function index(): Response
    {
        $cache = app(BlogContentCache::class);

        $collections = $cache->remember('collections.index', [], 300, function () {
            return Collection::published()
                ->withCount('posts')
                ->with([
                    'posts' => fn ($query) => $query
                        ->published()
                        ->select([
                            'posts.id',
                            'posts.title',
                            'posts.slug',
                            'posts.excerpt',
                            'posts.reading_time',
                            'posts.published_at',
                            'posts.category_id',
                            'posts.featured_image',
                            'posts.featured_image_alt',
                        ])
                        ->with('category:id,name,slug,color')
                        ->orderBy('collection_post.part_number'),
                ])
                ->orderBy('title')
                ->get()
                ->map(function (Collection $collection) {
                    $items = $collection->posts->map(fn ($post) => [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt,
                        'reading_time_text' => $post->reading_time_text,
                        'published_at' => $post->published_at,
                        'featured_image_url' => $post->featured_image_url,
                        'featured_image_alt' => $post->featured_image_alt,
                        'category' => $post->category?->toArray(),
                        'part_number' => (int) $post->pivot->part_number,
                    ])->values();

                    return [
                        'id' => $collection->id,
                        'title' => $collection->title,
                        'slug' => $collection->slug,
                        'description' => $collection->description,
                        'posts_count' => $collection->posts_count,
                        'cover_item' => $items->first(),
                        'latest_item' => $items->last(),
                    ];
                })
                ->values()
                ->all();
        });

        return Inertia::render('Blog/Collections', [
            'collections' => $collections,
        ]);
    }

    public function show(string $slug): Response
    {
        $cache = app(BlogContentCache::class);
        $currentSlug = request()->string('current')->toString();

        $data = $cache->remember('collections.show', [$slug], 300, function () use ($slug) {
            $collection = Collection::published()
                ->where('slug', $slug)
                ->with([
                    'posts' => fn ($query) => $query
                        ->published()
                        ->select([
                            'posts.id',
                            'posts.title',
                            'posts.slug',
                            'posts.excerpt',
                            'posts.reading_time',
                            'posts.published_at',
                            'posts.category_id',
                            'posts.featured_image',
                            'posts.featured_image_alt',
                        ])
                        ->with('category:id,name,slug,color')
                        ->orderBy('collection_post.part_number'),
                ])
                ->firstOrFail();

            return [
                'collection' => [
                    'id' => $collection->id,
                    'title' => $collection->title,
                    'slug' => $collection->slug,
                    'description' => $collection->description,
                    'items' => $collection->posts->map(fn ($post) => [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt,
                        'reading_time' => $post->reading_time,
                        'reading_time_text' => $post->reading_time_text,
                        'published_at' => $post->published_at,
                        'featured_image_url' => $post->featured_image_url,
                        'featured_image_alt' => $post->featured_image_alt,
                        'category' => $post->category?->toArray(),
                        'part_number' => (int) $post->pivot->part_number,
                    ])->values()->all(),
                ],
            ];
        });

        $currentItem = collect($data['collection']['items'])->firstWhere('slug', $currentSlug)
            ?? collect($data['collection']['items'])->first();

        $isFollowing = auth()->check()
            ? UserContentFollow::query()
                ->where('user_id', auth()->id())
                ->where('followable_type', Collection::class)
                ->where('followable_id', $data['collection']['id'])
                ->exists()
            : false;

        return Inertia::render('Blog/CollectionShow', [
            'collection' => array_merge($data['collection'], [
                'is_following' => $isFollowing,
                'current_item' => $currentItem,
                'total_parts' => count($data['collection']['items']),
            ]),
        ]);
    }
}
