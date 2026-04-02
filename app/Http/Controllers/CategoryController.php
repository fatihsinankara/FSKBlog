<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->get();

        return Inertia::render('Blog/Categories', [
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): Response
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->forCategory($slug)
            ->with(['tags'])
            ->latest('published_at')
            ->paginate(9);

        return Inertia::render('Blog/CategoryShow', [
            'category' => $category,
            'posts'    => $posts,
        ]);
    }
}
