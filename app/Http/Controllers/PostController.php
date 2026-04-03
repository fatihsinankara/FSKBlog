<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Comment;
use App\Models\Post;
use App\Support\BlogContentCache;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $page = request()->integer('page', 1);
        $cache = app(BlogContentCache::class);

        $data = $cache->remember('posts.index', ['page', $page], 300, function () {
            $featured = Post::published()->featured()
                ->select(BlogContentCache::FEATURED_POST_COLUMNS)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->first();

            $posts = Post::published()
                ->select(BlogContentCache::POST_LISTING_COLUMNS)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug'])
                ->latest('published_at')
                ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
                ->paginate(9)
                ->through(fn (Post $post) => $post->toArray());

            return [
                'featured' => $featured?->toArray(),
                'posts' => $posts->toArray(),
            ];
        });

        return Inertia::render('Blog/Index', $data);
    }

    public function show(string $slug): Response
    {
        $cache = app(BlogContentCache::class);

        $post = $cache->remember('post.show', [$slug], 600, function () use ($slug) {
            $post = Post::published()
                ->where('slug', $slug)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug', 'user:id,name'])
                ->firstOrFail();

            $post->append('rendered_body');

            return $post->toArray();
        });

        if (! session()->has('viewed_post_'.$post['id'])) {
            Post::whereKey($post['id'])->increment('views');
            $post['views'] = ($post['views'] ?? 0) + 1;
            session()->put('viewed_post_'.$post['id'], true);
        }

        $comments = Comment::query()
            ->where('post_id', $post['id'])
            ->approved()
            ->with(['user:id,name'])
            ->latest()
            ->paginate(20, ['*'], 'comments_page')
            ->withQueryString()
            ->through(fn (Comment $comment) => $comment->toArray());

        $related = Post::published()
            ->when($post['category_id'], fn ($q) => $q->where('category_id', $post['category_id']))
            ->where('id', '!=', $post['id'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'category_id', 'published_at'])
            ->with(['category:id,name,slug,color'])
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(fn (Post $relatedPost) => $relatedPost->toArray());

        return Inertia::render('Blog/Show', [
            'post' => array_merge($post, [
                'comments' => $comments->toArray(),
                'collection' => $this->resolveCollectionData($post['id']),
            ]),
            'related' => $related,
        ]);
    }

    public function search(): Response
    {
        $term = request()->input('q', '');
        $page = request()->integer('page', 1);
        $cache = app(BlogContentCache::class);

        $data = $cache->remember('posts.search', ['q', md5($term), 'page', $page], 300, function () use ($term) {
            $query = Post::published()
                ->select(BlogContentCache::POST_LISTING_COLUMNS)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug']);

            $posts = ($term ? $query->search($term) : $query->latest('published_at'))
                ->paginate(9)
                ->withQueryString()
                ->through(fn (Post $post) => $post->toArray());

            return [
                'posts' => $posts->toArray(),
                'query' => $term,
            ];
        });

        return Inertia::render('Blog/Search', [
            'posts' => $data['posts'],
            'query' => $data['query'],
        ]);
    }

    protected function resolveCollectionData(int $postId): ?array
    {
        $cache = app(BlogContentCache::class);

        return $cache->remember('post.collection', [$postId], 300, function () use ($postId) {
            $collection = Collection::published()
                ->whereHas('posts', fn ($query) => $query->where('posts.id', $postId))
                ->with([
                    'posts' => fn ($query) => $query
                        ->published()
                        ->select('posts.id', 'posts.title', 'posts.slug')
                        ->orderBy('collection_post.part_number'),
                ])
                ->first();

            if (! $collection) {
                return null;
            }

            $items = $collection->posts->map(fn (Post $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'part_number' => (int) $item->pivot->part_number,
                'is_current' => $item->id === $postId,
            ])->values();

            $currentIndex = $items->search(fn (array $item) => $item['is_current']);

            if ($currentIndex === false) {
                return null;
            }

            $previous = $currentIndex > 0 ? $items[$currentIndex - 1] : null;
            $next = $currentIndex < $items->count() - 1 ? $items[$currentIndex + 1] : null;

            return [
                'id' => $collection->id,
                'title' => $collection->title,
                'slug' => $collection->slug,
                'description' => $collection->description,
                'current_part' => $items[$currentIndex]['part_number'],
                'total_parts' => $items->count(),
                'items' => $items->all(),
                'previous' => $previous,
                'next' => $next,
            ];
        });
    }
}
