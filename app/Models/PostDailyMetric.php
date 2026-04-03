<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostDailyMetric extends Model
{
    protected $fillable = [
        'post_id',
        'date',
        'views',
        'new_bookmarks',
        'new_comments',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
