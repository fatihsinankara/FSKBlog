<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Zamanlanmış yazıları yayına al ve cache\'i temizle';

    public function handle(): int
    {
        $posts = Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('published_at', '>=', now()->subMinutes(5))
            ->get();

        if ($posts->isEmpty()) {
            $this->info('Yayına alınacak zamanlanmış yazı yok.');

            return self::SUCCESS;
        }

        Post::bumpContentCacheVersion();

        $this->info($posts->count().' yazı yayına alındı:');

        foreach ($posts as $post) {
            $this->line("  - {$post->title}");
        }

        return self::SUCCESS;
    }
}
