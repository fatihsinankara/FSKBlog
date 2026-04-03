<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PageAndMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array']);
        Cache::flush();
    }

    public function test_published_pages_are_publicly_visible_while_drafts_are_not(): void
    {
        $publishedPage = Page::create([
            'title' => 'Hakkimizda',
            'body' => 'Merhaba dunya',
            'status' => 'published',
        ]);

        $draftPage = Page::create([
            'title' => 'Gizli Taslak',
            'body' => 'Sadece admin gorecek',
            'status' => 'draft',
        ]);

        $this->get(route('pages.show', $publishedPage->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Page')
                ->where('page.title', 'Hakkimizda')
            );

        $this->get(route('pages.show', $draftPage->slug))
            ->assertNotFound();
    }

    public function test_admin_page_updates_regenerate_blank_slugs_and_sync_menu_links(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $page = Page::create([
            'title' => 'Ilk Baslik',
            'body' => 'Icerik',
            'status' => 'published',
        ]);

        MenuItem::create([
            'label' => 'Hakkimizda',
            'type' => 'page',
            'target' => $page->slug,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.pages.update', $page), [
                'title' => 'Yeni Hakkimizda',
                'slug' => '',
                'body' => 'Guncel icerik',
                'status' => 'published',
                'meta_title' => '',
                'meta_description' => '',
            ])
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();

        $this->assertSame('yeni-hakkimizda', $page->slug);
        $this->assertDatabaseHas('menu_items', [
            'label' => 'Yeni Hakkimizda',
            'target' => 'yeni-hakkimizda',
        ]);
    }

    public function test_shared_navigation_hides_page_links_for_unpublished_pages(): void
    {
        $publishedPage = Page::create([
            'title' => 'Iletisim',
            'body' => 'Bize ulasin',
            'status' => 'published',
        ]);

        $draftPage = Page::create([
            'title' => 'Taslak Menu Sayfasi',
            'body' => 'Taslak icerik',
            'status' => 'draft',
        ]);

        MenuItem::create([
            'label' => 'Iletisim',
            'type' => 'page',
            'target' => $publishedPage->slug,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Taslak Sayfa',
            'type' => 'page',
            'target' => $draftPage->slug,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'label' => 'Harici Kaynak',
            'type' => 'external',
            'target' => 'https://example.com/docs',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $this->createPublishedPost();

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/Index')
                ->has('nav.menu', 2)
                ->where('nav.menu.0.label', 'Iletisim')
                ->where('nav.menu.0.url', '/p/iletisim')
                ->where('nav.menu.1.label', 'Harici Kaynak')
            );
    }

    private function createPublishedPost(): Post
    {
        $author = User::factory()->create();
        $category = Category::create([
            'name' => 'Menu Test',
            'description' => 'Test kategori',
            'color' => '#123456',
        ]);

        return Post::create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Menu Test Yazisi',
            'body' => 'Icerik '.str_repeat('kelime ', 80),
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
