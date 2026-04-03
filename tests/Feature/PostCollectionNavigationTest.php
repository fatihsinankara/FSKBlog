<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PostCollectionNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_show_includes_collection_navigation_and_refreshes_after_collection_attach(): void
    {
        $posts = collect(range(1, 6))
            ->map(fn (int $number) => $this->createPublishedPost("Part {$number}"));

        $currentPost = $posts[2];

        $this->get(route('posts.show', $currentPost->slug))
            ->assertInertia(fn (Assert $page) => $page
                ->where('post.collection', null)
            );

        $collection = Collection::create([
            'title' => 'Vue Serisi',
            'description' => 'Parca parca ilerleyen bir seri.',
            'status' => 'published',
        ]);

        $collection->posts()->sync([
            $posts[0]->id => ['part_number' => 1],
            $posts[1]->id => ['part_number' => 2],
            $posts[2]->id => ['part_number' => 3],
            $posts[3]->id => ['part_number' => 4],
            $posts[4]->id => ['part_number' => 5],
            $posts[5]->id => ['part_number' => 6],
        ]);

        Post::bumpContentCacheVersion();

        $this->get(route('posts.show', $currentPost->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('post.collection.title', 'Vue Serisi')
                ->where('post.collection.current_part', 3)
                ->where('post.collection.total_parts', 6)
                ->where('post.collection.previous.slug', $posts[1]->slug)
                ->where('post.collection.next.slug', $posts[3]->slug)
                ->has('post.collection.items', 6)
            );
    }

    private function createPublishedPost(string $title): Post
    {
        $author = User::factory()->create();
        $category = Category::firstOrCreate([
            'name' => 'Seri Test',
        ], [
            'description' => 'Test kategori',
            'color' => '#222222',
        ]);

        return Post::create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => $title,
            'body' => 'Icerik '.str_repeat('kelime ', 80),
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
