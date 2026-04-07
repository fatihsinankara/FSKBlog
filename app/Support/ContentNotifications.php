<?php

namespace App\Support;

use App\Models\Collection;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCategoryPostNotification;
use App\Notifications\NewCollectionPostNotification;
use Illuminate\Support\Facades\Cache;

class ContentNotifications
{
    public function notifyPublishedPost(Post $post): void
    {
        if (! $post->isPubliclyVisible() || $post->published_notification_sent_at !== null) {
            return;
        }

        $post->loadMissing('category', 'collections');

        if ($post->category) {
            $users = User::query()
                ->whereHas('contentFollows', fn ($query) => $query
                    ->where('followable_type', $post->category::class)
                    ->where('followable_id', $post->category->id))
                ->get();

            if ($users->isNotEmpty()) {
                $users->each->notify(new NewCategoryPostNotification($post));
                $this->forgetNotificationCache($users);
            }
        }

        foreach ($post->collections as $collection) {
            $this->notifyCollectionFollowers($collection, $post, (int) $collection->pivot->part_number);
        }

        $post->forceFill([
            'published_notification_sent_at' => now(),
        ])->saveQuietly();
    }

    public function notifyCollectionFollowers(Collection $collection, Post $post, int $partNumber): void
    {
        if ($collection->status !== 'published' || $post->status !== 'published' || ! $post->published_at || $post->published_at->isFuture()) {
            return;
        }

        $users = User::query()
            ->whereHas('contentFollows', fn ($query) => $query
                ->where('followable_type', $collection::class)
                ->where('followable_id', $collection->id))
            ->get();

        if ($users->isNotEmpty()) {
            $users->each->notify(new NewCollectionPostNotification($collection, $post, $partNumber));
            $this->forgetNotificationCache($users);
        }
    }

    protected function forgetNotificationCache($users): void
    {
        $users->each(fn (User $user) => Cache::forget('user.'.$user->id.'.unread_notifications'));
    }
}
