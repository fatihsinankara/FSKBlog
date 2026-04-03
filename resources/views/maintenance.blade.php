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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@500;600&display=swap" rel="stylesheet">
        <style>
            :root {
                color-scheme: dark;
                --bg: #09090b;
                --panel: rgba(255, 255, 255, 0.06);
                --panel-border: rgba(255, 255, 255, 0.1);
                --text: #ffffff;
                --muted: rgba(228, 228, 231, 0.78);
                --soft: rgba(228, 228, 231, 0.58);
                --brand: #818cf8;
                --brand-soft: rgba(129, 140, 248, 0.18);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Inter', sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(99, 102, 241, 0.28), transparent 38%),
                    radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.16), transparent 30%),
                    var(--bg);
                color: var(--text);
            }

            .shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1.5rem;
            }

            .card {
                width: 100%;
                max-width: 42rem;
                border: 1px solid var(--panel-border);
                background: var(--panel);
                border-radius: 2rem;
                padding: 2rem;
                backdrop-filter: blur(18px);
                box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
            }

            .header {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 2rem;
            }

            .logo-image,
            .logo-fallback {
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 1rem;
                flex-shrink: 0;
            }

            .logo-image {
                object-fit: cover;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .logo-fallback {
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--brand-soft);
                color: #c7d2fe;
                font-weight: 700;
                letter-spacing: 0.04em;
                border: 1px solid rgba(129, 140, 248, 0.24);
            }

            .eyebrow {
                margin: 0 0 0.5rem;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.32em;
                text-transform: uppercase;
                color: rgba(199, 210, 254, 0.86);
            }

            .title {
                margin: 0;
                font-family: 'Lora', serif;
                font-size: clamp(2rem, 4vw, 2.6rem);
                line-height: 1.1;
            }

            .description {
                margin: 0;
                max-width: 36rem;
                font-size: 1rem;
                line-height: 2;
                color: var(--muted);
            }

            .notice {
                margin-top: 2rem;
                padding: 1rem 1.25rem;
                border-radius: 1.25rem;
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: rgba(0, 0, 0, 0.2);
                color: var(--soft);
                font-size: 0.95rem;
                line-height: 1.8;
            }

            @media (max-width: 640px) {
                .card {
                    padding: 1.5rem;
                    border-radius: 1.5rem;
                }

                .header {
                    align-items: flex-start;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="card">
                <div class="header">
                    @if($site->logo_url)
                        <img src="{{ $site->logo_url }}" alt="{{ $site->site_name }}" class="logo-image">
                    @else
                        <div class="logo-fallback">
                            {{ \Illuminate\Support\Str::substr($site->site_name, 0, 2) }}
                        </div>
                    @endif

                    <div>
                        <p class="eyebrow">Bakım Modu</p>
                        <h1 class="title">{{ $site->maintenance_title ?: 'Kısa bir bakım molasındayız' }}</h1>
                    </div>
                </div>

                <p class="description">
                    {{ $site->maintenance_message ?: 'Daha iyi bir deneyim için sistemi güncelliyoruz. Çok yakında geri döneceğiz.' }}
                </p>

                <div class="notice">
                    {{ $site->site_name }} kısa süreliğine erişime kapalı. Admin kullanıcıları giriş yaptıktan sonra siteyi kullanmaya devam edebilir.
                </div>
            </section>
        </main>
    </body>
</html>
