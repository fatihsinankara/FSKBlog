<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'database']);
        Cache::flush();
    }

    public function test_admin_can_view_cache_panel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Cache::put('cache-panel-demo', 'value', 600);

        $this->actingAs($admin)
            ->get(route('admin.cache.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Cache/Index')
                ->where('status.driver', 'database')
                ->has('recent_keys')
            );
    }

    public function test_admin_can_clear_application_cache_from_panel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Cache::put('cache-panel-demo', 'value', 600);
        $this->assertSame('value', Cache::get('cache-panel-demo'));

        $this->actingAs($admin)
            ->post(route('admin.cache.clear'))
            ->assertRedirect(route('admin.cache.index'))
            ->assertSessionHas('message', 'Uygulama cache temizlendi.');

        $this->assertNull(Cache::get('cache-panel-demo'));
    }
}
