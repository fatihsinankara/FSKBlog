<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Menus/Index', [
            'items' => $this->menuItems(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Menus/Create', [
            'pages' => $this->pages(),
            'categories' => $this->categories(),
            'parent_options' => $this->parentOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rulesFor($request));
        $validated['label'] = $this->resolveLabel($validated);

        if ($validated['type'] === 'custom') {
            $validated['target'] = $this->normalizeCustomPath($validated['target']);
        }

        if (! empty($validated['parent_id'])) {
            $error = $this->validateParent($validated['parent_id']);

            if ($error !== null) {
                return back()->withErrors(['parent_id' => $error]);
            }
        }

        MenuItem::create($validated);
        Cache::forget('nav.menu');

        return redirect()->route('admin.menus.index')->with('message', 'Menü öğesi eklendi.');
    }

    public function edit(MenuItem $menuItem): Response
    {
        return Inertia::render('Admin/Menus/Edit', [
            'menu_item' => $menuItem,
            'pages' => $this->pages(),
            'categories' => $this->categories(),
            'parent_options' => $this->parentOptions($menuItem),
        ]);
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate($this->rulesFor($request));
        $validated['label'] = $this->resolveLabel($validated);

        if ($validated['type'] === 'custom') {
            $validated['target'] = $this->normalizeCustomPath($validated['target']);
        }

        if (! empty($validated['parent_id'])) {
            if ((int) $validated['parent_id'] === $menuItem->id) {
                return back()->withErrors(['parent_id' => 'Bir öğe kendi ebeveyni olamaz.']);
            }

            $error = $this->validateParent($validated['parent_id']);

            if ($error !== null) {
                return back()->withErrors(['parent_id' => $error]);
            }
        }

        $menuItem->update($validated);
        Cache::forget('nav.menu');

        return redirect()->route('admin.menus.index')->with('message', 'Menü öğesi güncellendi.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();
        Cache::forget('nav.menu');

        return redirect()->route('admin.menus.index')->with('message', 'Menü öğesi silindi.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:menu_items,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['items'] as $data) {
            MenuItem::where('id', $data['id'])->update(['sort_order' => $data['sort_order']]);
        }

        Cache::forget('nav.menu');

        return back()->with('message', 'Sıralama güncellendi.');
    }

    protected function menuItems(): EloquentCollection
    {
        return MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    protected function pages(): EloquentCollection
    {
        return Page::published()
            ->select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();
    }

    protected function categories(): EloquentCollection
    {
        return Category::query()
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();
    }

    protected function parentOptions(?MenuItem $menuItem = null): EloquentCollection
    {
        return MenuItem::query()
            ->whereNull('parent_id')
            ->when($menuItem, fn ($query) => $query->whereKeyNot($menuItem->id))
            ->orderBy('sort_order')
            ->get(['id', 'label']);
    }

    protected function rulesFor(Request $request): array
    {
        $type = $request->input('type');
        $labelRules = ['nullable', 'string', 'max:100'];
        $targetRules = ['nullable', 'string', 'max:500'];

        if ($type === 'custom') {
            $labelRules[0] = 'required';
            $targetRules[] = 'required';
        } elseif ($type === 'external') {
            $labelRules[0] = 'required';
            $targetRules[] = 'required';
            $targetRules[] = 'url:http,https';
        } elseif ($type === 'page') {
            $targetRules[] = 'required';
            $targetRules[] = Rule::exists('pages', 'slug');
        } elseif ($type === 'category') {
            $targetRules[] = 'required';
            $targetRules[] = Rule::exists('categories', 'slug');
        } else {
            $targetRules[] = 'required';
        }

        return [
            'label' => $labelRules,
            'type' => ['required', 'in:page,category,custom,external'],
            'target' => $targetRules,
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'open_in_new_tab' => ['boolean'],
        ];
    }

    protected function validateParent(int|string $parentId): ?string
    {
        $parent = MenuItem::find($parentId);

        if ($parent && $parent->parent_id !== null) {
            return 'Maksimum 1 seviye alt menü desteklenir.';
        }

        return null;
    }

    protected function normalizeCustomPath(string $target): string
    {
        $target = trim($target);

        if ($target === '') {
            return '/';
        }

        if (filter_var($target, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $target) === 1) {
            return $target;
        }

        if (str_starts_with($target, '/') || str_starts_with($target, '#') || str_starts_with($target, '?')) {
            return $target;
        }

        return '/'.ltrim($target, '/');
    }

    protected function resolveLabel(array $validated): string
    {
        return match ($validated['type']) {
            'page' => Page::query()
                ->where('slug', $validated['target'])
                ->value('title') ?? $validated['label'] ?? '',
            'category' => Category::query()
                ->where('slug', $validated['target'])
                ->value('name') ?? $validated['label'] ?? '',
            default => trim((string) ($validated['label'] ?? '')),
        };
    }
}
