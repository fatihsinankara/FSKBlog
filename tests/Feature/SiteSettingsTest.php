<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_site_settings_and_public_pages_receive_shared_site_data(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                'site_name' => 'Yeni Kral Blog',
                'site_description' => 'Aciklama metni',
                'site_keywords' => 'laravel,vue,blog',
                'default_meta_title' => 'Yeni Kral Blog',
                'default_meta_description' => 'Varsayilan aciklama',
                'logo' => UploadedFile::fake()->image('logo.png'),
                'favicon' => UploadedFile::fake()->image('favicon.png', 64, 64),
                'default_og_image' => UploadedFile::fake()->image('og.png', 1200, 630),
                'custom_head_code' => '<meta name="verification" content="123">',
                'custom_body_end_code' => '<script>window.analyticsReady=true;</script>',
                'maintenance_enabled' => false,
            ])
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('site_settings', [
            'id' => 1,
            'site_name' => 'Yeni Kral Blog',
        ]);

        $this->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('site.site_name', 'Yeni Kral Blog')
                ->where('site.site_description', 'Aciklama metni')
            );
    }

    public function test_invalid_custom_snippets_are_rejected(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.settings.edit'))
            ->post(route('admin.settings.update'), [
                'site_name' => 'Test',
                'custom_head_code' => '<iframe src="https://example.com"></iframe>',
                'custom_body_end_code' => '<body>invalid</body>',
            ])
            ->assertRedirect(route('admin.settings.edit'))
            ->assertSessionHasErrors(['custom_head_code', 'custom_body_end_code']);
    }

    public function test_maintenance_mode_blocks_public_users_but_allows_admins(): void
    {
        SiteSetting::query()->create([
            'site_name' => 'FSK Blog',
            'maintenance_enabled' => true,
            'maintenance_title' => 'Bakim',
            'maintenance_message' => 'Test bakimi',
        ]);

        $this->get(route('home'))
            ->assertStatus(503)
            ->assertSee('Bakim');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk();
    }
}
