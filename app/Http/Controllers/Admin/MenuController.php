<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
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
        $items = MenuItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Menus/Index', [
            'items' => $items,
            'pages' => Page::published()->select('id', 'title', 'slug')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rulesFor($request));

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

        return back()->with('message', 'Menü öğesi eklendi.');
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate($this->rulesFor($request));

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

        return back()->with('message', 'Menü öğesi güncellendi.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();
        Cache::forget('nav.menu');

        return back()->with('message', 'Menü öğesi silindi.');
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

    protected function rulesFor(Request $request): array
    {
        $targetRules = ['nullable', 'string', 'max:500'];

        if ($request->input('type') === 'external') {
            $targetRules[] = 'required';
            $targetRules[] = 'url:http,https';
        } elseif ($request->input('type') === 'page') {
            $targetRules[] = 'required';
            $targetRules[] = Rule::exists('pages', 'slug');
        } else {
            $targetRules[] = 'required';
        }

        return [
            'label' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:page,custom,external'],
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

        if (str_starts_with($target, '/') || str_starts_with($target, '#') || str_starts_with($target, '?')) {
            return $target;
        }

        return '/'.ltrim($target, '/');
    }
}
