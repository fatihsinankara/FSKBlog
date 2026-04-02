<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function show(string $slug): Response
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->forTag($slug)
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return Inertia::render('Blog/TagShow', [
            'tag'   => $tag,
            'posts' => $posts,
        ]);
    }
}
