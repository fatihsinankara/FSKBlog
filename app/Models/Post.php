<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\MarkdownConverter;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'featured_image', 'featured_image_alt',
        'status', 'published_at', 'reading_time', 'meta_title', 'meta_description',
        'featured', 'category_id', 'user_id',
    ];

    protected $appends = ['featured_image_url', 'reading_time_text'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'featured' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug(Str::slug($post->title));
            }
        });

        static::saving(function (Post $post) {
            if ($post->isDirty('body')) {
                $wordCount = str_word_count(strip_tags($post->body));
                $post->reading_time = max(1, (int) ceil($wordCount / 200));
            }
        });

        static::saved(function (Post $post) {
            static::flushFrontendCaches($post);
        });

        static::deleted(function (Post $post) {
            static::flushFrontendCaches($post);
        });
    }

    public static function indexCacheVersion(): int
    {
        return static::contentCacheVersion();
    }

    public static function contentCacheVersion(): int
    {
        return (int) Cache::get('blog.content.version', 1);
    }

    public static function bumpContentCacheVersion(): int
    {
        $nextVersion = static::contentCacheVersion() + 1;

        Cache::forever('blog.content.version', $nextVersion);

        return $nextVersion;
    }

    protected static function uniqueSlug(string $slug): string
    {
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    protected static function flushFrontendCaches(Post $post): void
    {
        Cache::forget("post.{$post->id}.rendered");
        static::bumpContentCacheVersion();
    }

    // --- Accessors ---

    protected function renderedBody(): Attribute
    {
        return Attribute::get(function () {
            return Cache::remember("post.{$this->id}.rendered", 3600, function () {
                $converter = app(MarkdownConverter::class);

                return $converter->convert($this->body)->getContent();
            });
        });
    }

    protected function featuredImageUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null;
        });
    }

    protected function readingTimeText(): Attribute
    {
        return Attribute::get(function () {
            return $this->reading_time.' dk okuma';
        });
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)
            ->withPivot('part_number')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function dailyMetrics(): HasMany
    {
        return $this->hasMany(PostDailyMetric::class);
    }

    // --- Scopes ---

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeForCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeForTag(Builder $query, string $slug): Builder
    {
        return $query->whereHas('tags', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $driver = $query->getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return $query->where(function (Builder $searchQuery) use ($term) {
                $searchQuery
                    ->where('title', 'like', '%'.$term.'%')
                    ->orWhere('body', 'like', '%'.$term.'%');
            });
        }

        return $query->whereFullText(['title', 'body'], $term);
    }
}
