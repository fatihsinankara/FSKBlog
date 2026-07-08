<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_and_tag_sitemaps_only_include_terms_with_published_posts(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $publishedCategory = Category::create(['name' => 'Yayinda']);
        $emptyCategory = Category::create(['name' => 'Bos']);
        $draftOnlyCategory = Category::create(['name' => 'Taslak']);

        $publishedTag = Tag::create(['name' => 'Gorunur']);
        $emptyTag = Tag::create(['name' => 'Gizli']);
        $draftOnlyTag = Tag::create(['name' => 'Taslak Tag']);

        $publishedPost = Post::create([
            'user_id' => $admin->id,
            'category_id' => $publishedCategory->id,
            'title' => 'Yayindaki Yazi',
            'body' => 'Icerik',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $publishedPost->tags()->attach($publishedTag);

        $draftPost = Post::create([
            'user_id' => $admin->id,
            'category_id' => $draftOnlyCategory->id,
            'title' => 'Taslak Yazi',
            'body' => 'Icerik',
            'status' => 'draft',
            'published_at' => null,
        ]);
        $draftPost->tags()->attach($draftOnlyTag);

        $this->get(route('sitemap.categories'))
            ->assertOk()
            ->assertSee(route('categories.show', $publishedCategory->slug), false)
            ->assertDontSee(route('categories.show', $emptyCategory->slug), false)
            ->assertDontSee(route('categories.show', $draftOnlyCategory->slug), false);

        $this->get(route('sitemap.tags'))
            ->assertOk()
            ->assertSee(route('tags.show', $publishedTag->slug), false)
            ->assertDontSee(route('tags.show', $emptyTag->slug), false)
            ->assertDontSee(route('tags.show', $draftOnlyTag->slug), false);
    }
}
