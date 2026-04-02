<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ListingCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'database']);
        Cache::flush();
    }

    public function test_categories_index_remains_renderable_and_refreshes_after_category_changes(): void
    {
        $category = Category::create([
            'name' => 'Gelistirme',
            'description' => 'Notlar',
            'color' => '#111111',
        ]);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Categories')
                ->where('categories.0.name', 'Gelistirme')
            );

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Categories')
                ->where('categories.0.name', 'Gelistirme')
            );

        $category->update([
            'name' => 'Mimari',
        ]);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('categories.0.name', 'Mimari')
            );
    }

    public function test_category_show_remains_renderable_across_repeated_requests(): void
    {
        $post = $this->createPublishedPost([
            'title' => 'Kategori Yazisi',
        ]);

        $this->get(route('categories.show', $post->category->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/CategoryShow')
                ->where('category.slug', $post->category->slug)
                ->where('posts.data.0.title', 'Kategori Yazisi')
            );

        $this->get(route('categories.show', $post->category->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('category.slug', $post->category->slug)
                ->where('posts.data.0.title', 'Kategori Yazisi')
            );
    }

    public function test_tag_show_remains_renderable_across_repeated_requests(): void
    {
        $tag = Tag::create([
            'name' => 'Laravel',
        ]);

        $post = $this->createPublishedPost([
            'title' => 'Tag Yazisi',
        ]);

        $post->tags()->attach($tag->id);

        $this->get(route('tags.show', $tag->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/TagShow')
                ->where('tag.slug', $tag->slug)
                ->where('posts.data.0.title', 'Tag Yazisi')
            );

        $this->get(route('tags.show', $tag->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('tag.slug', $tag->slug)
                ->where('posts.data.0.title', 'Tag Yazisi')
            );
    }

    public function test_search_listing_remains_renderable_across_repeated_requests(): void
    {
        $this->createPublishedPost([
            'title' => 'Arama Listesi',
        ]);

        $this->get(route('search'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Search')
                ->where('posts.data.0.title', 'Arama Listesi')
            );

        $this->get(route('search'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('posts.data.0.title', 'Arama Listesi')
            );
    }

    private function createPublishedPost(array $overrides = []): Post
    {
        $author = User::factory()->create();
        $category = Category::create([
            'name' => 'Kategori',
            'description' => 'Test kategori',
            'color' => '#222222',
        ]);

        return Post::create(array_merge([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Liste Yazisi',
            'body' => 'Icerik '.str_repeat('kelime ', 80),
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }
}
