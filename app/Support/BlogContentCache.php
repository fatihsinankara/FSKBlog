<?php

namespace App\Support;

use App\Models\Post;
use Closure;
use Illuminate\Support\Facades\Cache;

class BlogContentCache
{
    public const POST_LISTING_COLUMNS = [
        'id',
        'title',
        'slug',
        'excerpt',
        'featured_image',
        'featured_image_alt',
        'category_id',
        'status',
        'published_at',
        'reading_time',
    ];

    public const FEATURED_POST_COLUMNS = [
        ...self::POST_LISTING_COLUMNS,
        'featured',
    ];

    public function remember(string $scope, array $segments, int $seconds, Closure $callback): mixed
    {
        return Cache::remember($this->key($scope, $segments), $seconds, $callback);
    }

    public function contentVersion(): int
    {
        return Post::contentCacheVersion();
    }

    public function key(string $scope, array $segments = []): string
    {
        $parts = array_merge([$scope, 'payload', 'v'.$this->contentVersion()], $this->normalizeSegments($segments));

        return implode('.', $parts);
    }

    protected function normalizeSegments(array $segments): array
    {
        return array_map(function (mixed $segment): string {
            if (is_bool($segment)) {
                return $segment ? '1' : '0';
            }

            if (is_scalar($segment) || $segment === null) {
                return (string) $segment;
            }

            return md5(json_encode($segment, JSON_THROW_ON_ERROR));
        }, $segments);
    }
}
