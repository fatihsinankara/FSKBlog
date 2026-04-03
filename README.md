# FSK Blog

FSK Blog, Laravel 13 + Inertia.js + Vue 3 ile geliştirilmiş modern bir blog ve içerik yönetim uygulamasıdır. Public tarafta hızlı, sade ve SEO dostu bir okuma deneyimi sunarken; admin panelinde yazı, kategori, tag, sayfa, koleksiyon, menü, yorum ve site ayarlarını tek merkezden yönetmeyi hedefler.

Bu repo yalnızca bir "blog teması" değil; içerik üretimi, editöryel akış, moderasyon, temel analitik, güvenlik başlıkları ve cache stratejisi düşünülmüş tam bir içerik platformu iskeletidir.

## Öne Çıkanlar

- Laravel 13 backend, Inertia.js köprüsü ve Vue 3 frontend
- Tailwind CSS v4 tabanlı modern arayüz
- Public blog, kategori, tag, koleksiyon ve statik sayfa akışları
- Admin panelden tam CRUD içerik yönetimi
- Markdown destekli yazı ve sayfa içeriği
- Yorum sistemi, admin onayı ve yorum beğenileri
- Bookmark sistemi ve kayıtlı içerik durum yönetimi
- Kategori ve koleksiyon takip akışı
- Newsletter abonelik ve güvenli abonelikten çıkış akışı
- RSS feed ve XML sitemap üretimi
- Cache destekli içerik listeleme ve render stratejisi
- Güvenlik başlıkları, throttle, bakım modu ve temel içerik korumaları
- PHPUnit testleri ile desteklenen stabil bir temel

## Ekran / Modül Özeti

### Public taraf

- Ana sayfa
- Yazı detay sayfası
- Kategori arşivleri
- Tag arşivleri
- Koleksiyon listesi ve koleksiyon detay akışı
- Statik sayfalar
- Arama sayfası
- RSS feed: `/feed.xml`
- Sitemap:
  - `/sitemap.xml`
  - `/sitemap/pages.xml`
  - `/sitemap/posts.xml`
  - `/sitemap/categories.xml`
  - `/sitemap/tags.xml`

### Üyelik ve kullanıcı etkileşimi

- Kayıt ol / giriş yap
- Profil güncelleme
- Şifre güncelleme
- Yazı bookmark'lama
- Yorum gönderme
- Yorum beğenme
- Kategori takip etme
- Koleksiyon takip etme

### Admin paneli

- Dashboard ve temel içerik metrikleri
- Yazı yönetimi
- Koleksiyon yönetimi
- Kategori yönetimi
- Tag yönetimi
- Sayfa yönetimi
- Menü yönetimi
- Yorum moderasyonu
- Cache paneli
- Site ayarları
- Bakım modu yönetimi

## Teknoloji Yığını

### Backend

- PHP 8.3+
- Laravel 13
- Inertia Laravel
- League CommonMark
- Laravel Sanctum

### Frontend

- Vue 3
- Inertia.js
- Vite
- Tailwind CSS v4
- Lucide Vue

### Geliştirici araçları

- PHPUnit
- Laravel Pint
- Laravel Breeze
- Laravel Pail

## Kurulum

### Gereksinimler

- PHP 8.3 veya üzeri
- Composer
- Node.js 20+ ve npm
- SQLite, MySQL veya PostgreSQL

Varsayılan `.env.example` yapılandırması SQLite, database cache, database session ve database queue ile gelir. Local geliştirme için en hızlı başlangıç yolu budur.

### Hızlı başlangıç

```bash
git clone <repo-url> fskblog
cd fskblog

composer install
npm install

cp .env.example .env
php artisan key:generate

mkdir -p database
touch database/database.sqlite

php artisan migrate --seed
php artisan storage:link
```

Ardından geliştirme ortamını başlat:

```bash
composer run dev
```

Bu komut aynı anda şunları başlatır:

- Laravel local server
- Queue listener
- Log izleme
- Vite dev server

### Tek komutla kurulum

İstersen hazır Composer script'ini de kullanabilirsin:

```bash
composer run setup
php artisan migrate --seed
php artisan storage:link
```

Not:
`composer run setup` migration çalıştırır ama seed etmez. Demo içerik ve admin kullanıcı için ayrıca `php artisan migrate --seed` çalıştırmak gerekir.

## Demo Kullanıcıları

Seeder varsayılan olarak bir admin kullanıcı üretir:

- E-posta: `admin@fskblog.com`
- Şifre: `changeme123`

Örnek içeriklerle birlikte bir normal kullanıcı da oluşturulur:

- E-posta: `okur@fskblog.com`
- Şifre: `changeme123`

İlk kurulumdan sonra bu bilgileri mutlaka değiştirmen önerilir.

Admin kullanıcı bilgilerini seed öncesinde environment ile özelleştirebilirsin:

```env
ADMIN_NAME=FSK
ADMIN_EMAIL=admin@fskblog.com
ADMIN_PASSWORD=super-secure-password
```

## Geliştirme Komutları

### Uygulama

```bash
composer run dev
php artisan serve
npm run dev
npm run build
```

### Veritabanı

```bash
php artisan migrate
php artisan migrate:fresh --seed
```

### Test

```bash
composer test
php artisan test
php artisan test --parallel
php artisan test --filter=CommentTest
```

### Format

```bash
vendor/bin/pint
vendor/bin/pint --test
```

## Proje Yapısı

```text
app/
├── Http/Controllers/        # Public ve admin controller'lar
├── Http/Middleware/         # Security headers, admin kontrolü, bakım modu
├── Models/                  # Eloquent modelleri
├── Notifications/           # Bildirimler
├── Policies/                # Authorization politikaları
├── Support/                 # Cache, metrics, site settings, sanitization yardımcıları
resources/
├── js/
│   ├── Components/          # Tekrar kullanılabilir Vue bileşenleri
│   ├── Layouts/             # App ve admin layout'ları
│   └── Pages/               # Inertia sayfaları
└── views/                   # Blade root view, feed, sitemap, bakım ekranı
routes/
└── web.php                  # Public ve admin route tanımları
database/
├── migrations/              # Şema
└── seeders/                 # Admin kullanıcı, site ayarı ve demo içerik seed'leri
tests/
└── Feature/                 # Uygulama davranışı odaklı testler
```

## İçerik Akışı

### Yazılar

- Admin panelden oluşturulur ve düzenlenir
- Markdown olarak saklanır
- Public tarafta render edilmiş HTML olarak sunulur
- `published` durumu ve `published_at` kontrolü ile yayın akışı yönetilir
- Kategori, tag ve koleksiyonlarla ilişkilendirilebilir

### Koleksiyonlar

- Serileştirilmiş içerik akışları için kullanılır
- Her yazı yalnızca tek bir koleksiyona bağlanabilir
- Part numarası mantığıyla sıralanır
- Public tarafta okuyucuya bölüm akışı sunar

### Sayfalar

- Blog dışı sabit içerikler için kullanılır
- Örn: hakkımızda, iletişim, kullanım koşulları
- Menü sistemine bağlanabilir

### Yorumlar

- Misafir veya giriş yapmış kullanıcı tarafından bırakılabilir
- Admin dışındaki kullanıcı yorumları onay bekler
- Tek seviyeli yanıt akışı vardır
- Spam azaltmak için honeypot ve throttle içerir

## Güvenlik ve Kararlılık

Projede temel güvenlik ve dayanıklılık katmanları hazır gelir:

- `admin` middleware ile yönetim paneli koruması
- CSP, HSTS, `X-Frame-Options`, `Referrer-Policy` gibi güvenlik başlıkları
- Arama, yorum ve newsletter uçlarında rate limit
- Session tabanlı görüntülenme sayacı koruması
- Yorum verisinde public payload daraltması
- Güvenli newsletter unsubscribe confirm akışı
- Admin ayarlarındaki özel snippet alanları için sıkı sanitization
- Bakım modu desteği

## Cache ve Performans

İçerik ağırlıklı sayfalarda kontrollü cache kullanımı vardır:

- Ana sayfa
- Arama sonuçları
- Kategori ve tag arşivleri
- Koleksiyon listeleme ve detay akışları
- Tekil yazı render çıktısı
- Sayfa render çıktısı
- RSS feed

İçerik güncellendiğinde ilgili cache katmanları temizlenecek şekilde yapı kurulmuştur. Bu sayede performans kazanımı ile içerik tutarlılığı arasında dengeli bir yaklaşım hedeflenir.

## Newsletter Altyapısı

Newsletter tarafı `App\Contracts\NewsletterSync` sözleşmesi üzerinden çalışır. Varsayılan olarak `NullNewsletterSync` kullanılır; yani dış servise bağlanmadan güvenli bir local akış sağlar.

Eğer ileride Mailchimp, Brevo veya başka bir servis bağlamak istersen:

1. `NewsletterSync` kontratını implement eden bir servis yaz.
2. `AppServiceProvider` içinde binding'i değiştir.
3. Gerekli API bilgilerini `.env` üzerinden yönet.

Bu yaklaşım sayesinde çekirdek uygulama akışı bozulmadan entegrasyon genişletilebilir.

## Site Ayarları

Admin panelindeki genel ayarlar bölümü şunları yönetir:

- Site adı ve açıklaması
- Varsayılan meta title / description
- Logo, favicon, OG image
- Head ve body sonu özel kod alanları
- Bakım modu başlığı ve mesajı

Bu alanlar public uygulamaya shared props ile taşınır ve layout seviyesinde kullanılır.

## Test Kapsamı

Feature testleri şu başlıklarda kapsama sağlar:

- Admin yetkilendirme
- Yazı, kategori, tag, koleksiyon ve menü yönetimi
- Authentication ve profil akışları
- Yorum sistemi
- Cache invalidation davranışları
- Page ve menu görünürlüğü
- Newsletter abonelik akışı
- Güvenlik odaklı snippet doğrulaması

## Prod'a Alırken Dikkat

Üretim ortamına geçmeden önce en azından şunları gözden geçir:

- `APP_ENV=production`
- `APP_DEBUG=false`
- Güçlü bir `APP_KEY`
- Gerçek `APP_URL`
- Uygun `MAIL_*` ayarları
- Queue worker yapılandırması
- `php artisan storage:link`
- Cache / session / queue için uygun store seçimi
- Varsayılan admin şifresinin değiştirilmesi

Önerilen optimizasyon komutları:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## Kısa Yol Haritası Fikirleri

- Gerçek e-posta servis entegrasyonu
- Medya kütüphanesi / görsel yönetimi
- Çok yazarlı editör akışı
- Draft preview
- Gelişmiş analitik ve raporlama
- Çok dilli içerik desteği

## Lisans

Bu proje MIT lisansı ile sunulmaktadır.
