<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::withCount('posts')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request, ImageProcessor $processor): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            $validated['image'] = $processor->process($request->file('image'), 'categories/images');
        }

        Category::create($validated);
        Cache::forget('nav.categories');

        return redirect()->route('admin.categories.index')->with('message', 'Kategori oluşturuldu.');
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category, ImageProcessor $processor): RedirectResponse
    {
        $validated = $request->validate($this->rules($category));

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $processor->process($request->file('image'), 'categories/images');
        } elseif ($request->boolean('remove_image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = null;
        }

        unset($validated['remove_image']);

        $category->update($validated);
        Cache::forget('nav.categories');

        return redirect()->route('admin.categories.index')->with('message', 'Kategori güncellendi.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        Cache::forget('nav.categories');

        return redirect()->route('admin.categories.index')->with('message', 'Kategori silindi.');
    }

    protected function rules(?Category $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'remove_image' => ['boolean'],
        ];
    }
}
