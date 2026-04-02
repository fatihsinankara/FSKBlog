<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class CacheController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Cache/Index', $this->buildPayload());
    }

    public function clear(): RedirectResponse
    {
        Cache::flush();

        return redirect()
            ->route('admin.cache.index')
            ->with('message', 'Uygulama cache temizlendi.');
    }

    protected function buildPayload(): array
    {
        $driver = (string) config('cache.default');
        $prefix = (string) config('cache.prefix', '');
        $supportsInspection = $driver === 'database'
            && Schema::hasTable('cache')
            && Schema::hasTable('cache_locks');

        $status = [
            'driver' => $driver,
            'prefix' => $prefix,
            'supports_inspection' => $supportsInspection,
            'entry_count' => null,
            'lock_count' => null,
            'blog_key_count' => null,
            'blog_content_version' => Post::contentCacheVersion(),
            'has_nav_categories' => Cache::has('nav.categories'),
            'inspected_at' => now()->toIso8601String(),
        ];

        $recentKeys = [];

        if ($supportsInspection) {
            $cacheQuery = DB::table('cache');
            $locksQuery = DB::table('cache_locks');

            if ($prefix !== '') {
                $cacheQuery->where('key', 'like', $prefix.'%');
                $locksQuery->where('key', 'like', $prefix.'%');
            }

            $status['entry_count'] = (clone $cacheQuery)->count();
            $status['lock_count'] = (clone $locksQuery)->count();
            $status['blog_key_count'] = (clone $cacheQuery)
                ->where(function ($query) use ($prefix) {
                    $query
                        ->where('key', 'like', $prefix.'blog.%')
                        ->orWhere('key', 'like', $prefix.'nav.categories%')
                        ->orWhere('key', 'like', $prefix.'post.%')
                        ->orWhere('key', 'like', $prefix.'posts.%')
                        ->orWhere('key', 'like', $prefix.'categories.%')
                        ->orWhere('key', 'like', $prefix.'tags.%');
                })
                ->count();

            $recentKeys = (clone $cacheQuery)
                ->orderByDesc('expiration')
                ->limit(12)
                ->get(['key', 'expiration'])
                ->map(fn ($entry) => [
                    'key' => $prefix !== '' && str_starts_with($entry->key, $prefix)
                        ? substr($entry->key, strlen($prefix))
                        : $entry->key,
                    'expires_at' => $entry->expiration
                        ? now()->setTimestamp((int) $entry->expiration)->toIso8601String()
                        : null,
                ])
                ->all();
        }

        return [
            'status' => $status,
            'recent_keys' => $recentKeys,
        ];
    }
}
