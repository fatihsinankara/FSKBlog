<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookmarkController extends Controller
{
    public function index(Request $request): Response
    {
        $bookmarks = $request->user()
            ->bookmarks()
            ->with([
                'post' => fn ($q) => $q
                    ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'reading_time', 'category_id', 'published_at', 'status'])
                    ->where('status', 'published')
                    ->with('category:id,name,slug,color'),
            ])
            ->latest()
            ->paginate(12);

        return Inertia::render('Blog/Bookmarks', [
            'bookmarks' => $bookmarks,
        ]);
    }

    public function toggle(Request $request, Post $post): RedirectResponse
    {
        $user = $request->user();

        $existing = Bookmark::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Yazı kaydedilenlerden çıkarıldı.';
        } else {
            Bookmark::create(['user_id' => $user->id, 'post_id' => $post->id]);
            $message = 'Yazı kaydedildi.';
        }

        return back()->with('message', $message);
    }
}
