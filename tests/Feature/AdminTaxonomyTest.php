<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTaxonomyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_category_create_and_edit_pages(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Laravel',
            'description' => 'Kategori',
            'color' => '#111111',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Categories/Create'));

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Categories/Edit')
                ->where('category.id', $category->id)
            );
    }

    public function test_category_store_and_update_redirect_to_index(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'PHP',
                'description' => 'Backend',
                'color' => '#123456',
                'icon' => 'code',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'PHP 8',
                'description' => 'Backend dili',
                'color' => '#654321',
                'icon' => 'server',
                'remove_image' => false,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'PHP 8',
            'slug' => 'php-8',
        ]);
    }

    public function test_category_with_posts_cannot_be_deleted_and_image_is_kept(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Silinemez',
            'image' => 'categories/images/locked.webp',
        ]);

        Storage::disk('public')->put($category->image, 'image');

        Post::create([
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'title' => 'Kategoriye Bagli Yazi',
            'body' => 'Icerik',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect()
            ->assertSessionHas('error', 'Bu kategoriye bağlı yazılar olduğu için kategori silinemez.');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
        Storage::disk('public')->assertExists('categories/images/locked.webp');
    }

    public function test_empty_category_can_be_deleted(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Bos Kategori',
            'image' => 'categories/images/empty.webp',
        ]);

        Storage::disk('public')->put($category->image, 'image');

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('message', 'Kategori silindi.');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
        Storage::disk('public')->assertMissing('categories/images/empty.webp');
    }

    public function test_admin_can_open_tag_create_and_edit_pages(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $tag = Tag::create([
            'name' => 'Vue',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.tags.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Tags/Create'));

        $this->actingAs($admin)
            ->get(route('admin.tags.edit', $tag))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Tags/Edit')
                ->where('tag.id', $tag->id)
            );
    }

    public function test_tag_update_refreshes_slug_and_redirects_to_index(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $tag = Tag::create([
            'name' => 'Laravel',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.tags.update', $tag), [
                'name' => 'Laravel 13',
            ])
            ->assertRedirect(route('admin.tags.index'));

        $tag->refresh();

        $this->assertSame('laravel-13', $tag->slug);
    }
}
