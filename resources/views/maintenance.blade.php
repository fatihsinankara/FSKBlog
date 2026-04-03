<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>{{ $site->maintenance_title ?: 'Bakım Modu' }} | {{ $site->site_name }}</title>
        <link rel="icon" href="{{ $site->favicon_url ?: asset('favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@500;600&display=swap" rel="stylesheet" />
        @if(app()->environment('testing'))
            <style>
                body { margin: 0; font-family: Inter, sans-serif; }
            </style>
        @else
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="min-h-screen bg-neutral-950 text-white antialiased">
        <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(99,102,241,0.28),_transparent_38%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.16),_transparent_30%)]"></div>
            <div class="relative w-full max-w-2xl rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
                <div class="mb-8 flex items-center gap-4">
                    @if($site->logo_url)
                        <img src="{{ $site->logo_url }}" alt="{{ $site->site_name }}" class="h-14 w-14 rounded-2xl object-cover ring-1 ring-white/10">
                    @else
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/20 text-lg font-semibold text-indigo-200 ring-1 ring-indigo-300/20">
                            {{ \Illuminate\Support\Str::substr($site->site_name, 0, 2) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-indigo-200/80">Bakım Modu</p>
                        <h1 class="mt-2 font-serif text-3xl text-white">
                            {{ $site->maintenance_title ?: 'Kısa bir bakım molasındayız' }}
                        </h1>
                    </div>
                </div>

                <p class="max-w-xl text-base leading-8 text-neutral-200/85">
                    {{ $site->maintenance_message ?: 'Daha iyi bir deneyim için sistemi güncelliyoruz. Çok yakında geri döneceğiz.' }}
                </p>

                <div class="mt-8 rounded-2xl border border-white/10 bg-black/20 px-5 py-4 text-sm text-neutral-300">
                    {{ $site->site_name }} kısa süreliğine erişime kapalı. Admin kullanıcıları giriş yaptıktan sonra siteyi kullanmaya devam edebilir.
                </div>
            </div>
        </main>
    </body>
</html>
