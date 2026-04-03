<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCategoryPostNotification extends Notification
{
    use Queueable;

    public function __construct(protected Post $post) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'category_post',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'category_name' => $this->post->category?->name,
            'category_slug' => $this->post->category?->slug,
        ];
    }
}
