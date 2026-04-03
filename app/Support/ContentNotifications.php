<?php

namespace App\Support;

use App\Models\Collection;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCategoryPostNotification;
use App\Notifications\NewCollectionPostNotification;

class ContentNotifications
{
    public function notifyPublishedPost(Post $post): void
    {
        $post->loadMissing('category', 'collections');

        if ($post->category) {
            $users = User::query()
                ->whereHas('contentFollows', fn ($query) => $query
                    ->where('followable_type', $post->category::class)
                    ->where('followable_id', $post->category->id))
                ->get();

            if ($users->isNotEmpty()) {
                $users->each->notify(new NewCategoryPostNotification($post));
            }
        }

        foreach ($post->collections as $collection) {
            $this->notifyCollectionFollowers($collection, $post, (int) $collection->pivot->part_number);
        }
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
        }
    }
}
