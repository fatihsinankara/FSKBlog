<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use App\Support\PostMetrics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookmarkController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->string('status')->toString();

        $bookmarks = $request->user()
            ->bookmarks()
            ->when(in_array($status, Bookmark::statuses(), true), fn ($query) => $query->where('status', $status))
            ->whereHas('post', fn ($query) => $query->published())
            ->with([
                'post' => fn ($q) => $q
                    ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'reading_time', 'category_id', 'published_at', 'status'])
                    ->published()
                    ->with('category:id,name,slug,color'),
            ])
            ->latest()
            ->paginate(12);

        return Inertia::render('Blog/Bookmarks', [
            'bookmarks' => $bookmarks,
            'filters' => [
                'status' => $status,
            ],
            'statuses' => Bookmark::statuses(),
        ]);
    }

    public function toggle(Request $request, Post $post, PostMetrics $metrics): RedirectResponse
    {
        abort_unless($post->isPubliclyVisible(), 404);

        $user = $request->user();

        $existing = Bookmark::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Yazı kaydedilenlerden çıkarıldı.';
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'status' => Bookmark::STATUS_SAVED,
            ]);
            $metrics->increment($post, 'new_bookmarks');
            $message = 'Yazı kaydedildi.';
        }

        return back()->with('message', $message);
    }

    public function updateStatus(Request $request, Bookmark $bookmark): RedirectResponse
    {
        abort_unless($bookmark->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Bookmark::statuses())],
        ]);

        $bookmark->update([
            'status' => $validated['status'],
        ]);

        return back()->with('message', 'Kaydetme durumu güncellendi.');
    }
}
