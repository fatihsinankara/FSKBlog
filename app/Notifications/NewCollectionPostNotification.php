<?php

namespace App\Notifications;

use App\Models\Collection;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCollectionPostNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Collection $collection,
        protected Post $post,
        protected int $partNumber,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'collection_post',
            'collection_id' => $this->collection->id,
            'collection_title' => $this->collection->title,
            'collection_slug' => $this->collection->slug,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'part_number' => $this->partNumber,
        ];
    }
}
