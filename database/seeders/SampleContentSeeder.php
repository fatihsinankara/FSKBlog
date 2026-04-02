<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleContentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', env('ADMIN_EMAIL', 'admin@fskblog.com'))->firstOrFail();

        $category = Category::firstOrCreate(
            ['slug' => 'gelistirme'],
            [
                'name'        => 'Geliştirme',
                'description' => 'Yazılım geliştirme üzerine yazılar.',
                'color'       => '#6366f1',
            ]
        );

        $body = <<<'MD'
# Merhaba Dünya

Bu blog sisteminin ilk yazısı. **Laravel**, **Inertia.js** ve **Vue 3** ile inşa edildi. Amaç hızlı, sade ve keyifli bir okuma deneyimi sunmak.

## Neler Var?

- GitHub Flavored Markdown desteği
- Kategori, tag ve tam metin arama
- Yorum moderasyonu
- Açık ve koyu tema desteği

## Neden Bu Yapı?

Laravel içerik ve veri tarafını güçlü şekilde çözüyor. Inertia.js ise ayrı bir API katmanı kurmadan Vue ile akıcı bir arayüz üretmemizi sağlıyor.

## Küçük Bir Örnek

```php
Route::get('/', [PostController::class, 'index'])->name('home');
```

> Minimal, hızlı ve düzenli bir blog deneyimi için sağlam bir temel.

Bir sonraki yazıda editör, içerik yapısı ve tasarım kararlarını daha detaylı anlatacağız.
MD;

        $post = Post::updateOrCreate(
            ['slug' => 'merhaba-dunya'],
            [
                'user_id'            => $user->id,
                'category_id'        => $category->id,
                'title'              => 'Merhaba Dünya',
                'excerpt'            => 'Bu blog sisteminin ilk yazısı. Laravel, Inertia.js ve Vue 3 ile inşa edildi.',
                'body'               => $body,
                'status'             => 'published',
                'published_at'       => now(),
                'featured'           => true,
                'meta_title'         => 'Merhaba Dünya — FSK Blog',
                'meta_description'   => 'Laravel, Inertia.js ve Vue 3 ile inşa edilmiş bu blogun ilk yazısı.',
            ]
        );

        Comment::firstOrCreate(
            [
                'post_id'    => $post->id,
                'guest_email' => 'ziyaretci@example.com',
            ],
            [
                'user_id'     => null,
                'guest_name'  => 'Meraklı Ziyaretçi',
                'body'        => 'Harika bir başlangıç! Blogu merakla takip edeceğim.',
                'is_approved' => true,
            ]
        );
    }
}
