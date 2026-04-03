<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();

        $bookmarks = $user->bookmarks();
        $follows = $user->contentFollows();

        $recentBookmarks = (clone $bookmarks)
            ->with([
                'post' => fn ($query) => $query
                    ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'featured_image_alt', 'category_id', 'published_at'])
                    ->with('category:id,name,slug,color'),
            ])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (Bookmark $bookmark) => [
                'id' => $bookmark->id,
                'status' => $bookmark->status,
                'created_at' => $bookmark->created_at,
                'post' => $bookmark->post?->toArray(),
            ])
            ->values();

        $recentNotifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->data['kind'] ?? 'system',
                'title' => $notification->data['post_title'] ?? $notification->data['collection_title'] ?? 'Yeni bildirim',
                'excerpt' => $notification->data['reply_excerpt']
                    ?? $notification->data['message']
                    ?? null,
                'created_at' => $notification->created_at,
                'is_read' => $notification->read_at !== null,
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'stats' => [
                'saved_bookmarks' => (clone $bookmarks)->where('status', Bookmark::STATUS_SAVED)->count(),
                'reading_bookmarks' => (clone $bookmarks)->where('status', Bookmark::STATUS_READING)->count(),
                'completed_bookmarks' => (clone $bookmarks)->where('status', Bookmark::STATUS_COMPLETED)->count(),
                'following_categories' => (clone $follows)->where('followable_type', Category::class)->count(),
                'following_collections' => (clone $follows)->where('followable_type', Collection::class)->count(),
                'unread_notifications' => $user->unreadNotifications()->count(),
            ],
            'recent_bookmarks' => $recentBookmarks,
            'recent_notifications' => $recentNotifications,
        ]);
    }
}
