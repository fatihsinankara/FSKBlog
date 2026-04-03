<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(string $slug): Response
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return Inertia::render('Blog/Page', [
            'page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'rendered_body' => $page->rendered_body,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'updated_at' => $page->updated_at,
            ],
        ]);
    }
}
