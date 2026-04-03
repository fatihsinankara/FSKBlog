<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\NewsletterSubscription;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExpansionExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookmark_status_can_be_updated(): void
    {
        $user = User::factory()->create();
        $post = $this->createPublishedPost('Kayit Testi');

        $this->actingAs($user)
            ->post(route('bookmarks.toggle', $post))
            ->assertRedirect();

        $bookmark = Bookmark::query()->firstOrFail();

        $this->assertSame(Bookmark::STATUS_SAVED, $bookmark->status);

        $this->actingAs($user)
            ->patch(route('bookmarks.status', $bookmark), [
                'status' => Bookmark::STATUS_READING,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'status' => Bookmark::STATUS_READING,
        ]);

        $this->actingAs($user)
            ->get(route('bookmarks.index', ['status' => Bookmark::STATUS_READING]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.status', Bookmark::STATUS_READING)
                ->has('bookmarks.data', 1)
            );
    }

    public function test_public_collection_page_highlights_current_item(): void
    {
        $posts = collect([
            $this->createPublishedPost('Birinci Bolum'),
            $this->createPublishedPost('Ikinci Bolum'),
            $this->createPublishedPost('Ucuncu Bolum'),
        ]);

        $collection = Collection::create([
            'title' => 'Vue Serisi',
            'description' => 'Adim adim ilerleyen seri.',
            'status' => 'published',
        ]);

        $collection->posts()->sync([
            $posts[0]->id => ['part_number' => 1],
            $posts[1]->id => ['part_number' => 2],
            $posts[2]->id => ['part_number' => 3],
        ]);

        $this->get(route('collections.show', ['slug' => $collection->slug, 'current' => $posts[1]->slug]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Blog/CollectionShow')
                ->where('collection.current_item.slug', $posts[1]->slug)
                ->where('collection.total_parts', 3)
                ->has('collection.items', 3)
            );
    }

    public function test_admin_reply_creates_notification_for_parent_comment_author(): void
    {
        $post = $this->createPublishedPost('Yanit Testi');
        $member = User::factory()->create();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $member->id,
            'body' => 'Ilk yorum',
            'is_approved' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('comments.store', $post), [
                'body' => 'Admin yaniti',
                'parent_id' => $comment->id,
                'website' => '',
            ])
            ->assertRedirect();

        $member->refresh();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'parent_id' => $comment->id,
            'user_id' => $admin->id,
            'is_approved' => true,
        ]);
        $this->assertSame(1, $member->notifications()->count());
        $this->assertSame('comment_reply', $member->notifications()->first()->data['kind']);
    }

    public function test_newsletter_subscription_can_be_created_and_cancelled(): void
    {
        $category = Category::create([
            'name' => 'Bulten',
            'description' => 'Bulten kategori',
            'color' => '#111111',
        ]);

        $this->post(route('newsletter.subscribe'), [
            'email' => 'okur@example.com',
            'name' => 'Okur',
            'categories' => [$category->id],
            'frequency' => 'weekly',
        ])->assertRedirect();

        $subscription = NewsletterSubscription::query()->firstOrFail();

        $this->assertSame('subscribed', $subscription->status);
        $this->assertSame([$category->id], $subscription->categories);

        $this->get(route('newsletter.unsubscribe', $subscription->unsubscribe_token))
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('newsletter_subscriptions', [
            'id' => $subscription->id,
            'status' => 'unsubscribed',
        ]);
    }

    public function test_followed_category_and_collection_publish_notifications_are_stored(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $category = Category::create([
            'name' => 'Takip',
            'description' => 'Takip kategori',
            'color' => '#222222',
        ]);

        $collection = Collection::create([
            'title' => 'Takip Serisi',
            'status' => 'published',
        ]);

        $this->actingAs($user)->post(route('categories.follow', $category))->assertRedirect();
        $this->actingAs($user)->post(route('collections.follow', $collection))->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Takip Edilen Yazi',
                'body' => 'Icerik '.str_repeat('kelime ', 70),
                'status' => 'published',
                'category_id' => $category->id,
            ])
            ->assertRedirect(route('admin.posts.index'));

        $post = Post::query()->where('title', 'Takip Edilen Yazi')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.collections.update', $collection), [
                'title' => $collection->title,
                'description' => '',
                'status' => 'published',
                'items' => [
                    ['post_id' => $post->id, 'part_number' => 1],
                ],
            ])
            ->assertRedirect(route('admin.collections.index'));

        $user->refresh();

        $this->assertSame(2, $user->notifications()->count());
        $kinds = $user->notifications->map(fn ($notification) => $notification->data['kind'])->all();

        $this->assertContains('category_post', $kinds);
        $this->assertContains('collection_post', $kinds);
    }

    private function createPublishedPost(string $title): Post
    {
        $author = User::factory()->create();
        $category = Category::firstOrCreate([
            'name' => 'Genel Test',
        ], [
            'description' => 'Test kategori',
            'color' => '#111111',
        ]);

        return Post::create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => $title,
            'body' => 'Icerik '.str_repeat('kelime ', 90),
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
