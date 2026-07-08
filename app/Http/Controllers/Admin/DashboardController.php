<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\NewsletterSubscription;
use App\Models\Post;
use App\Models\PostDailyMetric;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', Cache::remember('admin.dashboard.v1', 120, fn () => [
            'stats' => [
                'total_posts' => Post::count(),
                'published_posts' => Post::published()->count(),
                'draft_posts' => Post::where('status', 'draft')->count(),
                'total_views' => Post::sum('views'),
                'total_bookmarks' => Bookmark::count(),
                'pending_comments' => Comment::where('is_approved', false)->count(),
                'subscribers' => NewsletterSubscription::where('status', 'subscribed')->count(),
            ],
            'recent_posts' => Post::with('category:id,name,slug')
                ->select('id', 'title', 'slug', 'status', 'category_id', 'published_at', 'views', 'created_at')
                ->latest()
                ->take(5)
                ->get(),
            'top_posts_by_views' => Post::published()
                ->select('id', 'title', 'slug', 'views')
                ->orderByDesc('views')
                ->take(5)
                ->get(),
            'top_posts_by_bookmarks' => Post::published()
                ->select('id', 'title', 'slug')
                ->withCount('bookmarks')
                ->orderByDesc('bookmarks_count')
                ->take(5)
                ->get(),
            'top_posts_by_comments' => Post::published()
                ->select('id', 'title', 'slug')
                ->withCount(['comments as approved_comments_count' => fn ($query) => $query->approved()])
                ->orderByDesc('approved_comments_count')
                ->take(5)
                ->get(),
            'category_performance' => Category::query()
                ->select('id', 'name', 'slug', 'color')
                ->withCount(['posts as published_posts_count' => fn ($query) => $query->published()])
                ->withSum(['posts as total_views' => fn ($query) => $query->published()], 'views')
                ->orderByDesc('total_views')
                ->take(5)
                ->get(),
            'collection_performance' => Collection::query()
                ->select('id', 'title', 'slug', 'status')
                ->withCount('posts')
                ->withSum('posts as total_views', 'views')
                ->orderByDesc('total_views')
                ->take(5)
                ->get(),
            'daily_metrics' => PostDailyMetric::query()
                ->select([
                    'date',
                    DB::raw('SUM(views) as views'),
                    DB::raw('SUM(new_bookmarks) as new_bookmarks'),
                    DB::raw('SUM(new_comments) as new_comments'),
                ])
                ->groupBy('date')
                ->orderByDesc('date')
                ->take(14)
                ->get()
                ->reverse()
                ->values(),
            'newsletter_growth' => NewsletterSubscription::query()
                ->selectRaw('DATE(subscribed_at) as date, COUNT(*) as total')
                ->where('status', 'subscribed')
                ->whereNotNull('subscribed_at')
                ->groupBy('date')
                ->orderByDesc('date')
                ->take(14)
                ->get()
                ->reverse()
                ->values(),
        ]));
    }
}
