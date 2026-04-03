<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class MenuItem extends Model
{
    protected $fillable = [
        'label', 'type', 'target', 'parent_id', 'sort_order', 'is_active', 'open_in_new_tab',
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('nav.menu'));
        static::deleted(fn () => Cache::forget('nav.menu'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    /** Resolve the href URL for this item. */
    public function getUrlAttribute(): ?string
    {
        if (blank($this->target)) {
            return null;
        }

        return match ($this->type) {
            'page' => '/p/'.ltrim((string) $this->target, '/'),
            'external' => $this->target,
            default => str_starts_with((string) $this->target, '/')
                || str_starts_with((string) $this->target, '#')
                || str_starts_with((string) $this->target, '?')
                    ? $this->target
                    : '/'.ltrim((string) $this->target, '/'),
        };
    }
}
