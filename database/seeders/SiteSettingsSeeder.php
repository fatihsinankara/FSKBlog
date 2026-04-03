<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->firstOrCreate([
            'id' => 1,
        ], [
            'site_name' => 'FSK Blog',
            'site_description' => 'Yazılım, teknoloji ve daha fazlası.',
            'site_keywords' => 'yazılım, teknoloji, laravel, vue, inertia',
            'default_meta_title' => 'FSK Blog',
            'default_meta_description' => 'Yazılım, teknoloji ve daha fazlası.',
            'maintenance_title' => 'Kısa bir bakım molasındayız',
            'maintenance_message' => 'Daha iyi bir deneyim için sistemi güncelliyoruz. Çok yakında geri döneceğiz.',
        ]);
    }
}
