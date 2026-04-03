<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@fskblog.com'))->firstOrFail();

        $member = User::firstOrCreate(
            ['email' => 'okur@fskblog.com'],
            [
                'name' => 'Deniz Okur',
                'password' => bcrypt('changeme123'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        $development = Category::updateOrCreate(
            ['slug' => 'gelistirme'],
            [
                'name' => 'Geliştirme',
                'description' => 'Laravel, Vue ve üretim pratiği etrafında şekillenen teknik yazılar.',
                'color' => '#6366f1',
            ]
        );

        $product = Category::updateOrCreate(
            ['slug' => 'urun-ve-deneyim'],
            [
                'name' => 'Ürün ve Deneyim',
                'description' => 'İçerik stratejisi, editöryel bakış ve dijital ürün deneyimi üzerine notlar.',
                'color' => '#0f766e',
            ]
        );

        $posts = collect([
            [
                'slug' => 'laravel-projesinde-ilk-hafta-kazaniklari',
                'title' => 'Laravel Projesinde İlk Hafta Kazanımları',
                'excerpt' => 'Yeni bir Laravel projesinde ilk haftada alınan küçük ama etkili kararların, aylar sonrasındaki teslim hızını nasıl etkilediğini özetliyoruz.',
                'category_id' => $development->id,
                'featured' => true,
                'published_at' => now()->subDays(20),
                'meta_title' => 'Laravel Projesinde İlk Hafta Kazanımları',
                'meta_description' => 'Yeni Laravel projesinde ilk haftada alınan teknik kararların bakım ve teslim hızına etkisi.',
                'body' => <<<'MD'
# İlk hafta neden bu kadar kritik?

Bir projeye başlarken çoğu ekip büyük kararları sonraya bırakıyor. Oysa ilk hafta içinde alınan küçük kararlar; klasör düzeninden cache yaklaşımına, editör alışkanlıklarından içerik yayın akışına kadar her şeyi belirliyor.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere, sem sit amet ultrices convallis, purus nunc luctus massa, sit amet gravida lectus lacus vel eros. Sed non magna quis velit viverra hendrerit. Curabitur tincidunt nisl vitae lorem egestas, ac pretium tortor eleifend.

## Bizim ilk hafta checklist'imiz

- Route isimlerini en başta standartlaştırmak
- Admin ve public akışlarını ayırmak
- Listeleme ekranlarında gerekli kolonları açıkça seçmek
- İçerik cache'ini ilk günden düşünmek

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent aliquet lacinia erat, sed blandit sapien posuere nec. Phasellus eget porttitor orci, non dignissim nunc. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae.

## Neden küçük kararlar büyüyor?

Çünkü her yeni ekran, önceki kararların üstüne kuruluyor. Eğer isimlendirme, form yapısı ve veri taşıma biçimi tutarlıysa ekipteki herkes daha hızlı ilerliyor. Değilse, her işte yeniden karar vermek gerekiyor.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec vitae libero feugiat, interdum purus sit amet, posuere erat. Maecenas sodales enim vel molestie gravida. In hac habitasse platea dictumst.

> İyi bir başlangıç, kusursuz başlangıç değildir. Ama tekrar eden karar maliyetini düşüren başlangıçtır.
MD,
            ],
            [
                'slug' => 'vue-bilesenlerinde-sadeligi-korumak',
                'title' => 'Vue Bileşenlerinde Sadeliği Korumak',
                'excerpt' => 'Büyüyen bir panelde bileşen katmanını sade tutmak için kullandığımız birkaç pratik kuralı ve nedenlerini anlatıyoruz.',
                'category_id' => $development->id,
                'featured' => false,
                'published_at' => now()->subDays(16),
                'meta_title' => 'Vue Bileşenlerinde Sadeliği Korumak',
                'meta_description' => 'Vue bileşenlerinde okunabilirlik ve bakım maliyetini korumak için kullanılan pratik kurallar.',
                'body' => <<<'MD'
# Bileşen çoğaldıkça sadelik neden zorlaşıyor?

Vue tarafında hızla ilerlemek kolay. Asıl zor olan, üçüncü ayda dönüp baktığında aynı hızı koruyabilmek. Bunun için her bileşenin küçük, okunabilir ve tek sorumluluklu kalması gerekiyor.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras feugiat, purus sed volutpat ullamcorper, urna ipsum volutpat enim, ac ullamcorper mauris tortor at lacus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.

## Bizim ekipte çalışan üç ilke

1. Sayfa bileşeni akışı yönetir, detay bileşenleri görünümü taşır.
2. Form state'i mümkün olduğunca tek yerde tutulur.
3. Tek kullanımlık karmaşık parçalar bile gerektiğinde ayrı bileşene çıkarılır.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Etiam tristique porta arcu, eget faucibus nunc feugiat in. Morbi non justo eu magna vulputate aliquet.

## Derinlik değil, niyet önemli

Bileşen ağacı bazen derin olabilir. Sorun derinlik değil; her katmanın neden var olduğunun anlaşılır olmaması. Okuyan kişinin beş saniyede niyeti kavrayabildiği yapı genelde doğru yapıdır.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce mollis erat vitae mi suscipit, id vulputate velit commodo. Sed gravida efficitur erat, eget viverra lorem posuere at.
MD,
            ],
            [
                'slug' => 'icerik-yonetiminde-editoryel-ritim-kurmak',
                'title' => 'İçerik Yönetiminde Editöryel Ritim Kurmak',
                'excerpt' => 'Düzensiz içerik üretimi yerine sürdürülebilir bir yayın temposu kurmak isteyen ekipler için basit bir editöryel çerçeve.',
                'category_id' => $product->id,
                'featured' => false,
                'published_at' => now()->subDays(12),
                'meta_title' => 'İçerik Yönetiminde Editöryel Ritim Kurmak',
                'meta_description' => 'Tutarlı yayın temposu kurmak için kullanılan sade ve sürdürülebilir editöryel yöntemler.',
                'body' => <<<'MD'
# Daha çok içerik değil, daha iyi ritim

Bir blogun güçlü görünmesi için her gün içerik yayınlaması gerekmiyor. Asıl ihtiyaç, okuyucunun ne zaman geri döneceğini sezebildiği bir ritim oluşturmak.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis feugiat est eget enim tempus, eu rhoncus nisi auctor. Vestibulum posuere dolor quis libero venenatis, a ultricies enim hendrerit.

## Haftalık ritim örneği

- Pazartesi: araştırma ve taslak
- Çarşamba: düzenleme ve görsel hazırlığı
- Cuma: yayın ve dağıtım

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam ac massa at tortor egestas luctus. Praesent faucibus erat et odio aliquet, vitae tempus eros malesuada. Donec eu volutpat quam.

## Ritmi korumak için neyi azaltıyoruz?

Mükemmeliyet baskısını. Her yazının dev bir kılavuz olmasına gerek yok. Bazen kısa, net ve işe yarayan bir yazı; büyük ama geç çıkan bir yazıdan daha değerlidir.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed venenatis, nisi non feugiat luctus, lorem velit aliquet metus, vitae mattis velit lacus ut purus. Integer feugiat varius neque.
MD,
            ],
            [
                'slug' => 'tasarim-kararlarinda-tutarlilik-nasil-korunur',
                'title' => 'Tasarım Kararlarında Tutarlılık Nasıl Korunur?',
                'excerpt' => 'Yeni ekranlar eklendikçe ürünün karakterini kaybetmemesi için ekip içinde uyguladığımız birkaç basit kuralı paylaşıyoruz.',
                'category_id' => $product->id,
                'featured' => false,
                'published_at' => now()->subDays(9),
                'meta_title' => 'Tasarım Kararlarında Tutarlılık Nasıl Korunur?',
                'meta_description' => 'Büyüyen ürünlerde tasarım kararlarını tutarlı tutmak için uygulanabilir öneriler.',
                'body' => <<<'MD'
# Tutarlılık tesadüfen oluşmuyor

Bir ürünün olgun görünmesi çoğu zaman büyük animasyonlardan ya da parlak görsellerden değil, küçük kararların birbirini desteklemesinden geliyor. Boşluk, başlık tonu, kart yapısı ve geri bildirim dili buna dahil.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi viverra sem sed cursus aliquet. Sed sit amet efficitur risus. Suspendisse potenti. Donec ac risus leo.

## Bizim baktığımız dört sinyal

- Aynı işlev aynı görsel tonu taşıyor mu?
- Bilgi hiyerarşisi her ekranda benzer okunuyor mu?
- Boş durumlar ve hata mesajları aynı dili konuşuyor mu?
- Kullanıcı yeni bir ekran görünce yabancılık hissediyor mu?

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt velit ut ex vulputate dignissim. Integer luctus posuere justo, sed suscipit lacus pellentesque in.

## Tasarım bütünlüğü için en iyi alışkanlık

Yeni bir bileşen eklemeden önce mevcut sistemde yakın bir örnek aramak. Sıfırdan yaratmak bazen cazip geliyor ama ürün dili genelde yeniden icat edilerek değil, dikkatle sürdürülerek güçleniyor.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer tincidunt purus in risus posuere, in vestibulum augue vulputate. Nulla facilisi.
MD,
            ],
            [
                'slug' => 'laravelde-cache-stratejisini-kademeli-kurmak',
                'title' => 'Laravel’de Cache Stratejisini Kademeli Kurmak',
                'excerpt' => 'Her şeyi bir anda cache’lemek yerine, listeleme ve içerik sayfalarından başlayarak kademeli ilerlemenin neden daha güvenli olduğunu konuşuyoruz.',
                'category_id' => $development->id,
                'featured' => false,
                'published_at' => now()->subDays(5),
                'meta_title' => 'Laravel’de Cache Stratejisini Kademeli Kurmak',
                'meta_description' => 'Laravel projelerinde cache kullanımını kontrollü ve ölçülebilir biçimde kademeli kurma yaklaşımı.',
                'body' => <<<'MD'
# Cache'i sonradan eklemek zorunda değiliz

Performans sorunları görünür olduktan sonra cache eklemek mümkündür ama daha kontrollü yaklaşım, içerik akışını baştan göz önüne alarak küçük katmanlar oluşturmaktır.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus et quam vitae nibh hendrerit luctus. Nam et nunc enim. Integer vitae arcu massa. Integer et accumsan risus.

## Nereden başlıyoruz?

Önce sık ziyaret edilen ve geç değişen alanlardan:

- Anasayfa listelemeleri
- Kategori ve tag arşivleri
- Tekil içerik render çıktıları

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque suscipit tincidunt ligula, eu venenatis eros pellentesque sed. In consequat, nibh eu suscipit semper, purus ex malesuada libero, id cursus risus tortor vel lectus.

## Kademeli yaklaşımın avantajı

Ne kazandığını daha net görüyorsun. Ayrıca içerik güncellenince hangi cache'in temizleneceğini anlamak kolaylaşıyor. En önemlisi, ekipte kimse cache yüzünden korkup üretimden geri durmuyor.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas blandit commodo leo, a dictum eros luctus sed. Nunc feugiat dignissim est sed posuere.
MD,
            ],
        ])->map(function (array $data) use ($admin) {
            return Post::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id' => $admin->id,
                    'category_id' => $data['category_id'],
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'body' => $data['body'],
                    'status' => 'published',
                    'published_at' => $data['published_at'],
                    'featured' => $data['featured'],
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                ]
            );
        })->keyBy('slug');

        $collection = Collection::updateOrCreate(
            ['slug' => 'laravel-uygulamasini-olgunlastirmak'],
            [
                'title' => 'Laravel Uygulamasını Olgunlaştırmak',
                'description' => 'Kurulumdan cache stratejisine kadar aynı hattı takip eden mini bir seri.',
                'status' => 'published',
            ]
        );

        $collection->posts()->sync([
            $posts['laravel-projesinde-ilk-hafta-kazaniklari']->id => ['part_number' => 1],
            $posts['vue-bilesenlerinde-sadeligi-korumak']->id => ['part_number' => 2],
            $posts['laravelde-cache-stratejisini-kademeli-kurmak']->id => ['part_number' => 3],
        ]);

        $this->seedComments($posts, $member, $admin);
    }

    protected function seedComments($posts, User $member, User $admin): void
    {
        $comments = [
            [
                'post_slug' => 'laravel-projesinde-ilk-hafta-kazaniklari',
                'guest_name' => 'Selim',
                'guest_email' => 'selim@example.com',
                'body' => 'İlk hafta kararlarının uzun vadede bu kadar etkili olduğunu ekip içinde yeni yeni fark ediyoruz. Özellikle isimlendirme konusu çok tanıdık geldi.',
                'is_approved' => true,
            ],
            [
                'post_slug' => 'laravel-projesinde-ilk-hafta-kazaniklari',
                'user_id' => $member->id,
                'body' => 'Checklist kısmı çok iyi. Biz de benzer bir onboarding dökümanı hazırlıyoruz; ilham oldu.',
                'is_approved' => true,
            ],
            [
                'post_slug' => 'vue-bilesenlerinde-sadeligi-korumak',
                'guest_name' => 'Merve',
                'guest_email' => 'merve@example.com',
                'body' => 'Sayfa bileşeni akışı yönetir yaklaşımı çok temiz. Büyük projelerde buna dönmek gerçekten fark yaratıyor.',
                'is_approved' => true,
            ],
            [
                'post_slug' => 'icerik-yonetiminde-editoryel-ritim-kurmak',
                'guest_name' => 'Onur',
                'guest_email' => 'onur@example.com',
                'body' => 'Her yazının dev bir rehber olması gerekmiyor fikrine katılıyorum. Düzenli ritim daha güven veriyor.',
                'is_approved' => true,
            ],
            [
                'post_slug' => 'tasarim-kararlarinda-tutarlilik-nasil-korunur',
                'user_id' => $admin->id,
                'body' => 'Yeni ekran açmadan önce mevcut sisteme bakmak bizim ekipte de altın kural. Tutarlılığı ciddi biçimde koruyor.',
                'is_approved' => true,
            ],
            [
                'post_slug' => 'laravelde-cache-stratejisini-kademeli-kurmak',
                'guest_name' => 'Ayşe',
                'guest_email' => 'ayse@example.com',
                'body' => 'Cache tarafında önce listeleme ekranlarına odaklanmak mantıklı geldi. Bir anda her şeyi cache’lemek gerçekten korkutuyor.',
                'is_approved' => true,
            ],
        ];

        foreach ($comments as $data) {
            $post = $posts[$data['post_slug']];

            Comment::updateOrCreate(
                [
                    'post_id' => $post->id,
                    'body' => $data['body'],
                ],
                [
                    'user_id' => $data['user_id'] ?? null,
                    'guest_name' => $data['guest_name'] ?? null,
                    'guest_email' => $data['guest_email'] ?? null,
                    'is_approved' => $data['is_approved'],
                ]
            );
        }
    }
}
