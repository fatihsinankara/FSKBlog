<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
