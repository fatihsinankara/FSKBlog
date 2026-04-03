<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'is_admin' => $request->user()->is_admin,
                        'email_verified_at' => $request->user()->email_verified_at,
                        'unread_notifications_count' => $request->user()->unreadNotifications()->count(),
                    ]
                    : null,
            ],
            'site' => fn () => app(SiteSettings::class)->public(),
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'nav' => [
                'categories' => fn () => Cache::remember('nav.categories', 3600,
                    fn () => Category::select('id', 'name', 'slug', 'color', 'icon', 'image')->get()->toArray()
                ),
                'menu' => fn () => Cache::remember('nav.menu', 3600, function () {
                    $publishedPageSlugs = Page::published()
                        ->pluck('slug')
                        ->flip();
                    $categorySlugs = Category::query()
                        ->pluck('slug')
                        ->flip();

                    $items = MenuItem::with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                        ->whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();

                    return $items->map(function (MenuItem $item) use ($publishedPageSlugs, $categorySlugs) {
                        $children = $item->children
                            ->filter(fn (MenuItem $child) => $this->menuItemHasVisibleTarget($child, $publishedPageSlugs, $categorySlugs))
                            ->map(fn (MenuItem $child) => [
                                'id' => $child->id,
                                'label' => $child->label,
                                'type' => $child->type,
                                'url' => $child->url,
                                'open_in_new_tab' => $child->open_in_new_tab,
                            ])
                            ->values();

                        $hasVisibleLink = $this->menuItemHasVisibleTarget($item, $publishedPageSlugs, $categorySlugs);

                        if (! $hasVisibleLink && $children->isEmpty()) {
                            return null;
                        }

                        return [
                            'id' => $item->id,
                            'label' => $item->label,
                            'type' => $item->type,
                            'url' => $hasVisibleLink ? $item->url : null,
                            'open_in_new_tab' => $item->open_in_new_tab,
                            'children' => $children,
                        ];
                    })->filter()->values()->toArray();
                }),
            ],
        ];
    }

    protected function menuItemHasVisibleTarget(MenuItem $item, $publishedPageSlugs, $categorySlugs): bool
    {
        return match ($item->type) {
            'page' => $publishedPageSlugs->has($item->target),
            'category' => $categorySlugs->has($item->target),
            default => true,
        };
    }
}
