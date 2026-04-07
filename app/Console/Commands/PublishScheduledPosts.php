<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Support\ContentNotifications;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Zamanlanmış yazıları yayına al ve cache\'i temizle';

    public function handle(ContentNotifications $notifications): int
    {
        $posts = Post::query()
            ->published()
            ->whereNull('published_notification_sent_at')
            ->with(['category', 'collections'])
            ->orderBy('published_at')
            ->get();

        if ($posts->isEmpty()) {
            $this->info('Yayına alınacak zamanlanmış yazı yok.');

            return self::SUCCESS;
        }

        Post::bumpContentCacheVersion();

        $this->info($posts->count().' yazı yayına alındı:');

        foreach ($posts as $post) {
            $notifications->notifyPublishedPost($post);
            $this->line("  - {$post->title}");
        }

        return self::SUCCESS;
    }
}
