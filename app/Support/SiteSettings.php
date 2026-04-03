<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SiteSettings
{
    public const CACHE_KEY = 'site.settings.id.v2';

    public function current(): SiteSetting
    {
        $id = $this->resolveCachedId();

        return SiteSetting::query()->firstOrCreate(['id' => $id], $this->defaults());
    }

    public function public(): array
    {
        $settings = $this->current();

        return [
            'site_name' => $settings->site_name,
            'site_description' => $settings->site_description,
            'site_keywords' => $settings->site_keywords,
            'logo_url' => $settings->logo_url,
            'favicon_url' => $settings->favicon_url,
            'default_meta_title' => $settings->default_meta_title ?: $settings->site_name,
            'default_meta_description' => $settings->default_meta_description ?: $settings->site_description,
            'default_og_image_url' => $settings->default_og_image_url,
            'maintenance_enabled' => $settings->maintenance_enabled,
            'maintenance_title' => $settings->maintenance_title,
            'maintenance_message' => $settings->maintenance_message,
        ];
    }

    public function defaults(): array
    {
        return [
            'site_name' => config('app.name', 'FSK Blog'),
            'site_description' => 'Yazılım, teknoloji ve daha fazlası.',
            'site_keywords' => 'yazılım, teknoloji, laravel, vue, inertia',
            'default_meta_title' => config('app.name', 'FSK Blog'),
            'default_meta_description' => 'Yazılım, teknoloji ve daha fazlası.',
            'maintenance_title' => 'Kısa bir bakım molasındayız',
            'maintenance_message' => 'Daha iyi bir deneyim için sistemi güncelliyoruz. Çok yakında geri döneceğiz.',
        ];
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected function resolveCachedId(): int
    {
        try {
            $cached = Cache::get(self::CACHE_KEY);
        } catch (Throwable) {
            $this->forget();
            $cached = null;
        }

        if (is_int($cached) || (is_string($cached) && ctype_digit($cached))) {
            return (int) $cached;
        }

        if ($cached !== null) {
            $this->forget();
        }

        $id = (int) SiteSetting::query()->firstOrCreate(['id' => 1], $this->defaults())->getKey();
        Cache::forever(self::CACHE_KEY, $id);

        return $id;
    }
}
