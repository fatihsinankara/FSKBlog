<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminMenuManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_menu_create_and_edit_pages(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $page = Page::create([
            'title' => 'Hakkimizda',
            'body' => 'Icerik',
            'status' => 'published',
        ]);

        $menuItem = MenuItem::create([
            'label' => 'Ana Sayfa',
            'type' => 'page',
            'target' => $page->slug,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.menus.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Menus/Create')
                ->has('pages', 1)
                ->has('categories')
            );

        $this->actingAs($admin)
            ->get(route('admin.menus.edit', $menuItem))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Menus/Edit')
                ->where('menu_item.id', $menuItem->id)
            );
    }

    public function test_admin_can_store_update_and_reorder_menu_items_with_standard_routes(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $page = Page::create([
            'title' => 'Iletisim',
            'body' => 'Icerik',
            'status' => 'published',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.menus.store'), [
                'label' => '',
                'type' => 'page',
                'target' => $page->slug,
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'open_in_new_tab' => false,
            ])
            ->assertRedirect(route('admin.menus.index'));

        $menuItem = MenuItem::firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.menus.update', $menuItem), [
                'label' => 'İletişim Sayfası',
                'type' => 'custom',
                'target' => 'iletisim',
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'open_in_new_tab' => true,
            ])
            ->assertRedirect(route('admin.menus.index'));

        $secondItem = MenuItem::create([
            'label' => 'Blog',
            'type' => 'custom',
            'target' => '/blog',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.menus.reorder'), [
                'items' => [
                    ['id' => $menuItem->id, 'sort_order' => 0],
                    ['id' => $secondItem->id, 'sort_order' => 1],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('menu_items', [
            'id' => $menuItem->id,
            'label' => 'İletişim Sayfası',
            'target' => '/iletisim',
            'sort_order' => 0,
            'open_in_new_tab' => true,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'id' => $secondItem->id,
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_store_category_menu_item_with_automatic_label(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $category = Category::create([
            'name' => 'Gelistirme',
            'description' => 'Teknik icerik',
            'color' => '#111111',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.menus.store'), [
                'label' => '',
                'type' => 'category',
                'target' => $category->slug,
                'parent_id' => null,
                'sort_order' => 0,
                'is_active' => true,
                'open_in_new_tab' => false,
            ])
            ->assertRedirect(route('admin.menus.index'));

        $this->assertDatabaseHas('menu_items', [
            'type' => 'category',
            'target' => $category->slug,
            'label' => $category->name,
        ]);
    }
}
