<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Support\BlogContentCache;
use App\Support\PostMetrics;
use Illuminate\Support\Collection as SupportCollection;
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

    public function show(string $slug, PostMetrics $metrics): Response
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
            $metrics->increment($post['id'], 'views');
            $post['views'] = ($post['views'] ?? 0) + 1;
            session()->put('viewed_post_'.$post['id'], true);
        }

        $bookmark = auth()->check()
            ? Bookmark::where('user_id', auth()->id())->where('post_id', $post['id'])->first()
            : null;

        $comments = Comment::query()
            ->where('post_id', $post['id'])
            ->topLevel()
            ->approved()
            ->with(['user:id,name'])
            ->with([
                'replies' => fn ($query) => $query
                    ->approved()
                    ->with('user:id,name')
                    ->withCount('likes'),
            ])
            ->withCount('likes')
            ->latest()
            ->paginate(20, ['*'], 'comments_page')
            ->withQueryString()
            ->through(fn (Comment $comment) => $this->publicCommentPayload($comment));

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
                'is_bookmarked' => (bool) $bookmark,
                'bookmark_status' => $bookmark?->status,
            ]),
            'related' => $related,
        ]);
    }

    public function search(): Response
    {
        $term = trim((string) request()->input('q', ''));
        $page = request()->integer('page', 1);
        $filters = [
            'q' => $term,
            'category' => request()->string('category')->toString(),
            'tag' => request()->string('tag')->toString(),
            'collection' => request()->string('collection')->toString(),
            'year' => request()->string('year')->toString(),
            'reading_time' => request()->string('reading_time')->toString(),
            'sort' => request()->string('sort')->toString() ?: 'latest',
        ];
        $cache = app(BlogContentCache::class);

        $data = $cache->remember('posts.search', [$filters, 'page', $page], 300, function () use ($filters) {
            $query = Post::published()
                ->select(BlogContentCache::POST_LISTING_COLUMNS)
                ->with(['category:id,name,slug,color', 'tags:id,name,slug']);

            if ($filters['q']) {
                $query->search($filters['q']);
            }

            if ($filters['category']) {
                $query->forCategory($filters['category']);
            }

            if ($filters['tag']) {
                $query->forTag($filters['tag']);
            }

            if ($filters['collection']) {
                $query->whereHas('collections', fn ($collectionQuery) => $collectionQuery->where('slug', $filters['collection']));
            }

            if ($filters['year']) {
                $query->whereYear('published_at', (int) $filters['year']);
            }

            match ($filters['reading_time']) {
                'short' => $query->where('reading_time', '<=', 5),
                'medium' => $query->whereBetween('reading_time', [6, 10]),
                'long' => $query->where('reading_time', '>=', 11),
                default => null,
            };

            match ($filters['sort']) {
                'oldest' => $query->oldest('published_at'),
                'most_viewed' => $query->orderByDesc('views')->orderByDesc('published_at'),
                default => $query->latest('published_at'),
            };

            $posts = $query
                ->paginate(9)
                ->withQueryString()
                ->through(fn (Post $post) => $post->toArray());

            return [
                'posts' => $posts->toArray(),
                'filters' => $filters,
            ];
        });

        return Inertia::render('Blog/Search', [
            'posts' => $data['posts'],
            'query' => $data['filters']['q'],
            'filters' => $data['filters'],
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug'])->toArray(),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name', 'slug'])->toArray(),
            'collections' => Collection::published()->orderBy('title')->get(['id', 'title', 'slug'])->toArray(),
            'years' => Post::published()
                ->get(['published_at'])
                ->map(fn (Post $post) => $post->published_at?->format('Y'))
                ->filter()
                ->unique()
                ->sortDesc()
                ->values()
                ->all(),
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
                'current_slug' => $items[$currentIndex]['slug'],
            ];
        });
    }

    protected function publicCommentPayload(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'body' => $comment->body,
            'author_name' => $comment->author_name,
            'created_at' => $comment->created_at?->toJSON(),
            'likes_count' => $comment->likes_count ?? 0,
            'replies' => $this->publicReplyPayload($comment->replies),
        ];
    }

    protected function publicReplyPayload(SupportCollection $replies): array
    {
        return $replies->map(fn (Comment $reply) => [
            'id' => $reply->id,
            'body' => $reply->body,
            'author_name' => $reply->author_name,
            'created_at' => $reply->created_at?->toJSON(),
            'likes_count' => $reply->likes_count ?? 0,
        ])->values()->all();
    }
}
