<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'color', 'icon', 'image'];

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? Storage::url($this->image) : null,
        );
    }

    protected static function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: 'kategori';
        $candidate = $base;
        $count = 1;

        while (static::when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = "{$base}-{$count}";
            $count++;
        }

        return $candidate;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = static::uniqueSlug(Str::slug($category->name));
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = static::uniqueSlug(Str::slug($category->name), $category->id);
            }
        });

        static::saved(function () {
            Cache::forget('nav.categories');
            Post::bumpContentCacheVersion();
        });

        static::deleted(function () {
            Cache::forget('nav.categories');
            Post::bumpContentCacheVersion();
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
