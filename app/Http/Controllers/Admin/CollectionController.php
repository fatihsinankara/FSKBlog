<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Post;
use App\Support\ContentNotifications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function index(): Response
    {
        $collections = Collection::query()
            ->withCount('posts')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Collections/Index', [
            'collections' => $collections,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Collections/Create', [
            'available_posts' => $this->availablePosts(),
        ]);
    }

    public function store(Request $request, ContentNotifications $notifications): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $collection = Collection::create(Arr::except($validated, ['items']));
        $newItems = $this->syncItems($collection, $validated['items'] ?? []);
        $this->notifyNewItems($collection, $newItems, $notifications);

        return redirect()
            ->route('admin.collections.index')
            ->with('message', 'Koleksiyon oluşturuldu.');
    }

    public function edit(Collection $collection): Response
    {
        return Inertia::render('Admin/Collections/Edit', [
            'collection' => $collection->load([
                'posts' => fn ($query) => $query
                    ->select('posts.id', 'posts.title', 'posts.slug', 'posts.published_at')
                    ->orderBy('collection_post.part_number'),
            ]),
            'available_posts' => $this->availablePosts($collection),
        ]);
    }

    public function update(Request $request, Collection $collection, ContentNotifications $notifications): RedirectResponse
    {
        $validated = $this->validatePayload($request, $collection);

        $collection->update(Arr::except($validated, ['items']));
        $newItems = $this->syncItems($collection, $validated['items'] ?? []);
        $this->notifyNewItems($collection, $newItems, $notifications);

        return redirect()
            ->route('admin.collections.index')
            ->with('message', 'Koleksiyon güncellendi.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->delete();

        return redirect()
            ->route('admin.collections.index')
            ->with('message', 'Koleksiyon silindi.');
    }

    protected function availablePosts(?Collection $collection = null): array
    {
        $assignedIds = $collection
            ? $collection->posts()->pluck('posts.id')
            : collect();

        return Post::query()
            ->where(function ($query) use ($assignedIds) {
                $query->published();

                if ($assignedIds->isNotEmpty()) {
                    $query->orWhereIn('id', $assignedIds);
                }
            })
            ->select('id', 'title', 'slug', 'published_at', 'category_id')
            ->with('category:id,name')
            ->latest('published_at')
            ->get()
            ->toArray();
    }

    protected function validatePayload(Request $request, ?Collection $collection = null): array
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', Rule::unique('collections', 'title')->ignore($collection?->id)],
            'description' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', 'in:draft,published'],
            'items' => ['nullable', 'array'],
            'items.*.post_id' => ['required', 'integer', 'distinct', 'exists:posts,id'],
            'items.*.part_number' => ['required', 'integer', 'min:1', 'distinct'],
        ], [
            'items.*.post_id.distinct' => 'Bir yazı koleksiyona yalnızca bir kez eklenebilir.',
            'items.*.part_number.distinct' => 'Her part numarası koleksiyon içinde benzersiz olmalı.',
        ]);

        $validator->after(function ($validator) use ($request, $collection) {
            $items = collect($request->input('items', []));
            $postIds = $items->pluck('post_id')->filter()->map(fn ($id) => (int) $id)->values();

            if ($postIds->isEmpty()) {
                return;
            }

            $publishedIds = Post::published()
                ->whereIn('id', $postIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            $alreadyAttachedIds = $collection
                ? $collection->posts()->pluck('posts.id')->map(fn ($id) => (int) $id)
                : collect();

            $allowedIds = $publishedIds
                ->merge($alreadyAttachedIds)
                ->unique()
                ->values();

            if ($allowedIds->count() !== $postIds->unique()->count()) {
                $validator->errors()->add('items', 'Yalnızca yayındaki yazılar koleksiyonlara eklenebilir.');
            }

            $assignedPostIds = DB::table('collection_post')
                ->whereIn('post_id', $postIds)
                ->when($collection, fn ($query) => $query->where('collection_id', '!=', $collection->id))
                ->pluck('post_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($assignedPostIds->isNotEmpty()) {
                $titles = Post::query()
                    ->whereIn('id', $assignedPostIds)
                    ->pluck('title')
                    ->join(', ');

                $validator->errors()->add('items', "Bu yazılar zaten başka bir koleksiyonda: {$titles}");
            }
        });

        return $validator->validate();
    }

    protected function syncItems(Collection $collection, array $items): array
    {
        $existingIds = $collection->posts()->pluck('posts.id')->map(fn ($id) => (int) $id)->all();
        $syncData = collect($items)
            ->sortBy('part_number')
            ->mapWithKeys(fn (array $item) => [
                (int) $item['post_id'] => ['part_number' => (int) $item['part_number']],
            ])
            ->all();

        $collection->posts()->sync($syncData);
        Post::bumpContentCacheVersion();

        return collect($syncData)
            ->reject(fn (array $pivot, int $postId) => in_array($postId, $existingIds, true))
            ->map(fn (array $pivot) => (int) $pivot['part_number'])
            ->all();
    }

    protected function notifyNewItems(Collection $collection, array $newItems, ContentNotifications $notifications): void
    {
        if ($collection->status !== 'published' || $newItems === []) {
            return;
        }

        $posts = Post::query()
            ->whereIn('id', array_keys($newItems))
            ->get()
            ->keyBy('id');

        foreach ($newItems as $postId => $partNumber) {
            $post = $posts->get((int) $postId);

            if (! $post) {
                continue;
            }

            $notifications->notifyCollectionFollowers($collection, $post, (int) $partNumber);
        }
    }
}
