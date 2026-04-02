<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomepageCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'database']);
        Cache::flush();
    }

    public function test_homepage_remains_renderable_across_repeated_requests_with_database_cache(): void
    {
        $featured = $this->createPublishedPost([
            'title' => 'One Featured Post',
            'featured' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Index')
                ->where('featured.title', $featured->title)
                ->has('posts.data', 0)
            );

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Index')
                ->where('featured.title', $featured->title)
                ->has('posts.data', 0)
            );
    }

    public function test_homepage_cache_is_refreshed_after_post_changes(): void
    {
        $post = $this->createPublishedPost([
            'title' => 'Before Update',
            'featured' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('featured.title', 'Before Update'));

        $post->update([
            'title' => 'After Update',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('featured.title', 'After Update'));
    }

    private function createPublishedPost(array $overrides = []): Post
    {
        $author = User::factory()->create();
        $category = Category::create([
            'name' => 'Ana Sayfa',
            'description' => 'Ana sayfa kategori',
            'color' => '#111111',
        ]);

        return Post::create(array_merge([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Cached Post',
            'body' => 'Icerik '.str_repeat('kelime ', 80),
            'status' => 'published',
            'published_at' => now(),
            'featured' => false,
        ], $overrides));
    }
}
