<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_comments_are_stored_as_pending(): void
    {
        $post = $this->createPublishedPost();

        $this->from(route('posts.show', $post->slug))
            ->post(route('comments.store', $post->id), [
                'guest_name' => 'Ziyaretci',
                'guest_email' => 'ziyaretci@example.com',
                'body' => 'Misafir yorumu',
                'website' => '',
            ])
            ->assertRedirect(route('posts.show', $post->slug));

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'guest_email' => 'ziyaretci@example.com',
            'is_approved' => false,
        ]);
    }

    public function test_regular_user_comments_are_stored_as_pending(): void
    {
        $post = $this->createPublishedPost();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('posts.show', $post->slug))
            ->post(route('comments.store', $post->id), [
                'body' => 'Uye yorumu',
                'website' => '',
            ])
            ->assertRedirect(route('posts.show', $post->slug));

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'is_approved' => false,
        ]);
    }

    public function test_admin_comments_are_auto_approved(): void
    {
        $post = $this->createPublishedPost();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('posts.show', $post->slug))
            ->post(route('comments.store', $post->id), [
                'body' => 'Admin yorumu',
                'website' => '',
            ])
            ->assertRedirect(route('posts.show', $post->slug));

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $admin->id,
            'is_approved' => true,
        ]);
    }

    public function test_honeypot_blocks_comment_submission(): void
    {
        $post = $this->createPublishedPost();

        $this->from(route('posts.show', $post->slug))
            ->post(route('comments.store', $post->id), [
                'guest_name' => 'Bot',
                'guest_email' => 'bot@example.com',
                'body' => 'Spam yorum',
                'website' => 'https://spam.example.com',
            ])
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_rate_limit_is_enforced(): void
    {
        $post = $this->createPublishedPost();

        foreach (range(1, 5) as $attempt) {
            $this->from(route('posts.show', $post->slug))
                ->post(route('comments.store', $post->id), [
                    'guest_name' => "Misafir {$attempt}",
                    'guest_email' => "misafir{$attempt}@example.com",
                    'body' => "Yorum {$attempt}",
                    'website' => '',
                ])
                ->assertRedirect(route('posts.show', $post->slug));
        }

        $this->from(route('posts.show', $post->slug))
            ->post(route('comments.store', $post->id), [
                'guest_name' => 'Misafir 6',
                'guest_email' => 'misafir6@example.com',
                'body' => 'Yorum 6',
                'website' => '',
            ])
            ->assertSessionHasErrors('body');
    }

    public function test_post_comments_are_paginated(): void
    {
        $post = $this->createPublishedPost();

        foreach (range(1, 25) as $index) {
            Comment::create([
                'post_id' => $post->id,
                'guest_name' => "Misafir {$index}",
                'guest_email' => "misafir{$index}@example.com",
                'body' => "Onayli yorum {$index}",
                'is_approved' => true,
            ]);
        }

        $this->get(route('posts.show', $post->slug))
            ->assertInertia(fn (Assert $page) => $page
                ->where('post.comments.per_page', 20)
                ->where('post.comments.total', 25)
                ->has('post.comments.data', 20)
            );
    }

    public function test_public_comment_payload_does_not_expose_guest_email_addresses(): void
    {
        $post = $this->createPublishedPost();

        Comment::create([
            'post_id' => $post->id,
            'guest_name' => 'Gizli Misafir',
            'guest_email' => 'gizli@example.com',
            'body' => 'Gorunur yorum',
            'is_approved' => true,
        ]);

        $this->get(route('posts.show', $post->slug))
            ->assertInertia(fn (Assert $page) => $page
                ->has('post.comments.data', 1)
                ->where('post.comments.data.0.author_name', 'Gizli Misafir')
                ->missing('post.comments.data.0.guest_email')
            )
            ->assertDontSee('gizli@example.com');
    }

    public function test_comments_cannot_be_added_to_unpublished_posts(): void
    {
        $post = $this->createPublishedPost([
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->post(route('comments.store', $post->id), [
            'guest_name' => 'Ziyaretci',
            'guest_email' => 'ziyaretci@example.com',
            'body' => 'Gizli yazi yorumu',
            'website' => '',
        ])->assertNotFound();

        $this->assertDatabaseCount('comments', 0);
    }

    private function createPublishedPost(array $overrides = []): Post
    {
        $author = User::factory()->create();
        $category = Category::create([
            'name' => 'Genel',
            'description' => 'Genel kategori',
            'color' => '#111111',
        ]);

        return Post::create(array_merge([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Ornek Yazi',
            'body' => 'Icerik '.str_repeat('kelime ', 60),
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }
}
