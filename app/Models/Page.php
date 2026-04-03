<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use League\CommonMark\MarkdownConverter;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'body', 'status', 'meta_title', 'meta_description',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Page $page) {
            if (empty($page->slug)) {
                $page->slug = static::uniqueSlug(Str::slug($page->title), $page->id);
            }
        });

        static::updated(function (Page $page) {
            if ($page->wasChanged('slug')) {
                MenuItem::query()
                    ->where('type', 'page')
                    ->where('target', $page->getOriginal('slug'))
                    ->update(['target' => $page->slug]);
            }

            if ($page->wasChanged(['title', 'slug'])) {
                MenuItem::query()
                    ->where('type', 'page')
                    ->where('target', $page->slug)
                    ->update(['label' => $page->title]);
            }
        });

        static::saved(function (Page $page) {
            Cache::forget("page.{$page->id}.rendered");
            Cache::forget('nav.menu');
        });

        static::deleted(function (Page $page) {
            Cache::forget("page.{$page->id}.rendered");
            Cache::forget('nav.menu');
        });
    }

    protected static function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $original = $slug ?: 'sayfa';
        $candidate = $original;
        $count = 1;

        while (static::query()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = "{$original}-{$count}";
            $count++;
        }

        return $candidate;
    }

    protected function renderedBody(): Attribute
    {
        return Attribute::get(function () {
            return Cache::remember("page.{$this->id}.rendered", 3600, function () {
                $converter = app(MarkdownConverter::class);

                return $converter->convert($this->body ?? '')->getContent();
            });
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
