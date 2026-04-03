<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Collection extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Collection $collection) {
            if (blank($collection->slug)) {
                $collection->slug = static::uniqueSlug(Str::slug($collection->title));
            }
        });

        static::updating(function (Collection $collection) {
            if ($collection->isDirty('title') && ! $collection->isDirty('slug')) {
                $collection->slug = static::uniqueSlug(Str::slug($collection->title), $collection->id);
            }
        });

        static::saved(function () {
            Post::bumpContentCacheVersion();
        });

        static::deleted(function () {
            Post::bumpContentCacheVersion();
        });
    }

    protected static function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: 'koleksiyon';
        $candidate = $base;
        $count = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = "{$base}-{$count}";
            $count++;
        }

        return $candidate;
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)
            ->withPivot('part_number')
            ->withTimestamps()
            ->orderBy('collection_post.part_number');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
