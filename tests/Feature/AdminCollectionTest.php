<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_collection_with_part_numbers(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $first = $this->createPublishedPost('Birinci Yazi');
        $second = $this->createPublishedPost('Ikinci Yazi');

        $this->actingAs($admin)
            ->post(route('admin.collections.store'), [
                'title' => 'Laravel Serisi',
                'description' => 'Temelden ileri seviyeye seri yazilar.',
                'status' => 'published',
                'items' => [
                    ['post_id' => $first->id, 'part_number' => 1],
                    ['post_id' => $second->id, 'part_number' => 2],
                ],
            ])
            ->assertRedirect(route('admin.collections.index'));

        $collection = Collection::query()->first();

        $this->assertNotNull($collection);
        $this->assertDatabaseHas('collections', [
            'title' => 'Laravel Serisi',
            'status' => 'published',
        ]);
        $this->assertDatabaseHas('collection_post', [
            'collection_id' => $collection->id,
            'post_id' => $first->id,
            'part_number' => 1,
        ]);
        $this->assertDatabaseHas('collection_post', [
            'collection_id' => $collection->id,
            'post_id' => $second->id,
            'part_number' => 2,
        ]);
    }

    public function test_post_cannot_be_assigned_to_multiple_collections(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $post = $this->createPublishedPost('Tek Yazilik Seri');

        $collection = Collection::create([
            'title' => 'Ilk Seri',
            'status' => 'published',
        ]);

        $collection->posts()->sync([
            $post->id => ['part_number' => 1],
        ]);

        $this->actingAs($admin)
            ->from(route('admin.collections.index'))
            ->post(route('admin.collections.store'), [
                'title' => 'Ikinci Seri',
                'status' => 'published',
                'items' => [
                    ['post_id' => $post->id, 'part_number' => 1],
                ],
            ])
            ->assertRedirect(route('admin.collections.index'))
            ->assertSessionHasErrors('items');
    }

    private function createPublishedPost(string $title): Post
    {
        $author = User::factory()->create();
        $category = Category::firstOrCreate([
            'name' => 'Koleksiyon Test',
        ], [
            'description' => 'Test kategori',
            'color' => '#111111',
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
