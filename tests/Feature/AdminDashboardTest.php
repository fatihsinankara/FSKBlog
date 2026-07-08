<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_payload_is_cached_briefly(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Ilk Yazi',
            'body' => 'Icerik',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->where('stats.total_posts', 1)
            );

        $this->assertTrue(Cache::has('admin.dashboard.v1'));

        Post::create([
            'user_id' => $admin->id,
            'title' => 'Ikinci Yazi',
            'body' => 'Icerik',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->where('stats.total_posts', 1)
            );
    }
}
