<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_user_management_pages(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Users/Index')
                ->has('users.data')
            );

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Users/Create'));

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $member))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Users/Edit')
                ->where('user.id', $member->id)
            );
    }

    public function test_admin_can_create_update_and_delete_users(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Deneme Uye',
                'email' => 'uye@example.com',
                'password' => 'super-secret-password',
                'password_confirmation' => 'super-secret-password',
                'is_admin' => false,
                'email_verified' => true,
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'uye@example.com')->firstOrFail();

        $this->assertTrue((bool) $user->email_verified_at);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Deneme Admin',
                'email' => 'adminaday@example.com',
                'password' => '',
                'password_confirmation' => '',
                'is_admin' => true,
                'email_verified' => false,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Deneme Admin',
            'email' => 'adminaday@example.com',
            'is_admin' => true,
        ]);

        $this->assertNull($user->fresh()->email_verified_at);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_remove_own_admin_role_or_delete_self_from_admin_panel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'password_confirmation' => '',
                'is_admin' => false,
                'email_verified' => true,
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('is_admin');

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');
    }
}
