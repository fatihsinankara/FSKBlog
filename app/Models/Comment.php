<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = [
        'post_id', 'user_id', 'guest_name', 'guest_email', 'body', 'is_approved',
    ];

    protected $appends = ['author_name'];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    protected function authorName(): Attribute
    {
        return Attribute::get(function () {
            return $this->user?->name ?? $this->guest_name ?? 'Anonim';
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }
}
