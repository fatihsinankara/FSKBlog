<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_description',
        'site_keywords',
        'logo',
        'favicon',
        'default_meta_title',
        'default_meta_description',
        'default_og_image',
        'custom_head_code',
        'custom_body_end_code',
        'maintenance_enabled',
        'maintenance_title',
        'maintenance_message',
    ];

    protected $appends = ['logo_url', 'favicon_url', 'default_og_image_url'];

    protected function casts(): array
    {
        return [
            'maintenance_enabled' => 'boolean',
        ];
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->logo ? url(Storage::disk('public')->url($this->logo)) : null);
    }

    protected function faviconUrl(): Attribute
    {
        return Attribute::get(fn () => $this->favicon ? url(Storage::disk('public')->url($this->favicon)) : null);
    }

    protected function defaultOgImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->default_og_image ? url(Storage::disk('public')->url($this->default_og_image)) : null);
    }
}
