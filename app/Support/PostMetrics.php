<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PostMetrics
{
    public function increment(Post|int $post, string $column, int $amount = 1, ?Carbon $date = null): void
    {
        $postId = $post instanceof Post ? $post->id : $post;
        $day = ($date ?? now())->toDateString();

        DB::table('post_daily_metrics')->updateOrInsert([
            'post_id' => $postId,
            'date' => $day,
        ], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('post_daily_metrics')
            ->where('post_id', $postId)
            ->where('date', $day)
            ->increment($column, $amount, [
                'updated_at' => now(),
            ]);
    }
}
