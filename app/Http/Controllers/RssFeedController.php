<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class RssFeedController extends Controller
{
    public function index(): Response
    {
        $posts = Cache::remember('rss.feed', 600, function () {
            return Post::published()
                ->select(['id', 'title', 'slug', 'excerpt', 'published_at', 'category_id'])
                ->with('category:id,name,slug')
                ->latest('published_at')
                ->take(20)
                ->get()
                ->toArray();
        });

        $xml = view('feed', ['posts' => $posts, 'siteUrl' => config('app.url')])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
