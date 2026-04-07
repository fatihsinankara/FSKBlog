<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ImageProcessor;
use App\Support\ContentNotifications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::with(['category:id,name,slug', 'tags:id,name,slug'])
            ->select(['id', 'title', 'slug', 'status', 'category_id', 'user_id', 'featured', 'published_at', 'views', 'created_at'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Posts/Create', [
            'categories' => Category::select('id', 'name')->get(),
            'tags' => Tag::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request, ContentNotifications $notifications, ImageProcessor $processor): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:4096'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $uploadedImage = $request->file('featured_image');
        unset($validated['featured_image']);

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create(array_merge($validated, ['user_id' => $request->user()->id]));

        if ($uploadedImage) {
            $post->update([
                'featured_image' => $processor->processWithSlug($uploadedImage, 'posts/images', $post->slug),
            ]);
        }

        $post->tags()->sync($request->input('tag_ids', []));

        if ($this->isVisible($post)) {
            $notifications->notifyPublishedPost($post->loadMissing('category', 'collections'));
        }

        Cache::forget('nav.categories');

        return redirect()->route('admin.posts.index')->with('message', 'Yazı oluşturuldu.');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post->load('tags'),
            'categories' => Category::select('id', 'name')->get(),
            'tags' => Tag::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Post $post, ContentNotifications $notifications, ImageProcessor $processor): RedirectResponse
    {
        $wasVisible = $this->isVisible($post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:4096'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'remove_featured_image' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $processor->processWithSlug(
                $request->file('featured_image'),
                'posts/images',
                $post->slug,
            );
        } else {
            unset($validated['featured_image']);
        }

        if (($validated['remove_featured_image'] ?? false) && $post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
            $validated['featured_image'] = null;
        }

        if ($validated['status'] === 'published' && empty($validated['published_at']) && ! $post->published_at) {
            $validated['published_at'] = now();
        }

        unset($validated['remove_featured_image']);

        $post->update($validated);
        $post->tags()->sync($request->input('tag_ids', []));

        if (! $wasVisible && $this->isVisible($post->fresh())) {
            $notifications->notifyPublishedPost($post->fresh()->loadMissing('category', 'collections'));
        }

        return redirect()->route('admin.posts.index')->with('message', 'Yazı güncellendi.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('message', 'Yazı silindi.');
    }

    protected function isVisible(Post $post): bool
    {
        return $post->isPubliclyVisible();
    }
}
