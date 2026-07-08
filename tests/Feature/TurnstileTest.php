<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TurnstileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.turnstile.site_key' => '1x00000000000000000000AA',
            'services.turnstile.secret_key' => '1x0000000000000000000000000000000AA',
            'services.turnstile.verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        ]);
    }

    public function test_login_requires_valid_turnstile_when_enabled(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertSessionHasErrors('cf_turnstile_response');

        $this->assertGuest();

        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf_turnstile_response' => 'valid-token',
        ])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        Http::assertSent(fn ($request) => $request['response'] === 'valid-token'
            && $request['secret'] === '1x0000000000000000000000000000000AA');
    }

    public function test_register_rejects_failed_turnstile(): void
    {
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false]),
        ]);

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cf_turnstile_response' => 'bad-token',
        ])
            ->assertSessionHasErrors('cf_turnstile_response');

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_password_reset_request_validates_turnstile(): void
    {
        Notification::fake();
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);

        $user = User::factory()->create();

        $this->post(route('password.email'), [
            'email' => $user->email,
            'cf_turnstile_response' => 'valid-token',
        ])
            ->assertSessionDoesntHaveErrors();

        Notification::assertCount(1);
        Http::assertSent(fn ($request) => $request['response'] === 'valid-token');
    }

    public function test_guest_comments_require_turnstile_but_authenticated_comments_do_not(): void
    {
        $post = $this->publishedPost();

        $this->post(route('comments.store', $post), [
            'guest_name' => 'Misafir',
            'guest_email' => 'misafir@example.com',
            'body' => 'Guzel bir yazi.',
        ])
            ->assertSessionHasErrors('cf_turnstile_response');

        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);

        $this->post(route('comments.store', $post), [
            'guest_name' => 'Misafir',
            'guest_email' => 'misafir@example.com',
            'body' => 'Guzel bir yazi.',
            'cf_turnstile_response' => 'valid-token',
        ])
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'guest_email' => 'misafir@example.com',
            'is_approved' => false,
        ]);

        Http::fake();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('comments.store', $post), [
                'body' => 'Kullanici yorumu.',
            ])
            ->assertSessionDoesntHaveErrors();

        Http::assertNothingSent();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body' => 'Kullanici yorumu.',
        ]);
    }

    protected function publishedPost(): Post
    {
        return Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Turnstile Test Yazisi',
            'body' => 'Icerik',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
