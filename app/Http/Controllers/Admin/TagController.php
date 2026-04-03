<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Tags/Index', [
            'tags' => Tag::withCount('posts')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Tags/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('message', 'Tag oluşturuldu.');
    }

    public function edit(Tag $tag): Response
    {
        return Inertia::render('Admin/Tags/Edit', [
            'tag' => $tag,
        ]);
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate($this->rules($tag));

        $tag->update($validated);

        return redirect()->route('admin.tags.index')->with('message', 'Tag güncellendi.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('message', 'Tag silindi.');
    }

    protected function rules(?Tag $tag = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('tags', 'name')->ignore($tag?->id)],
        ];
    }
}
