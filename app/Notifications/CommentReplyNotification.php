<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentReplyNotification extends Notification
{
    use Queueable;

    public function __construct(protected Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'comment_reply',
            'comment_id' => $this->comment->id,
            'post_title' => $this->comment->post?->title,
            'post_slug' => $this->comment->post?->slug,
            'reply_author' => $this->comment->author_name,
            'reply_excerpt' => str($this->comment->body)->limit(140)->toString(),
        ];
    }
}
