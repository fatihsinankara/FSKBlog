<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPostUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_post_without_new_image_keeps_existing_featured_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Yonetim',
            'description' => 'Yonetim kategori',
            'color' => '#111111',
        ]);

        Storage::disk('public')->put('posts/images/existing-cover.jpg', 'cover');

        $post = Post::create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Gorselli Yazi',
            'body' => 'Eski icerik '.str_repeat('kelime ', 60),
            'featured_image' => 'posts/images/existing-cover.jpg',
            'featured_image_alt' => 'Kapak',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.posts.update', $post), [
                'title' => 'Guncel Baslik',
                'body' => 'Yeni icerik '.str_repeat('kelime ', 60),
                'excerpt' => 'Yeni ozet',
                'status' => 'published',
                'category_id' => $category->id,
                'featured' => false,
                'featured_image_alt' => 'Kapak',
                'meta_title' => '',
                'meta_description' => '',
                'remove_featured_image' => false,
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post->refresh();

        $this->assertSame('posts/images/existing-cover.jpg', $post->featured_image);
        Storage::disk('public')->assertExists('posts/images/existing-cover.jpg');
    }

    public function test_admin_can_explicitly_remove_featured_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Yonetim',
            'description' => 'Yonetim kategori',
            'color' => '#111111',
        ]);

        Storage::disk('public')->put('posts/images/removable-cover.jpg', 'cover');

        $post = Post::create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Silinecek Gorsel',
            'body' => 'Icerik '.str_repeat('kelime ', 60),
            'featured_image' => 'posts/images/removable-cover.jpg',
            'featured_image_alt' => 'Kapak',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.posts.update', $post), [
                'title' => 'Silinecek Gorsel',
                'body' => 'Guncel icerik '.str_repeat('kelime ', 60),
                'excerpt' => 'Ozet',
                'status' => 'published',
                'category_id' => $category->id,
                'featured' => false,
                'featured_image_alt' => '',
                'meta_title' => '',
                'meta_description' => '',
                'remove_featured_image' => true,
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post->refresh();

        $this->assertNull($post->featured_image);
        Storage::disk('public')->assertMissing('posts/images/removable-cover.jpg');
    }
}
