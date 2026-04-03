<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>Bülten Ayrılma Onayı | {{ $site->site_name }}</title>
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
                --danger: #fb7185;
                --danger-soft: rgba(251, 113, 133, 0.18);
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

            .eyebrow {
                margin: 0 0 0.75rem;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.32em;
                text-transform: uppercase;
                color: rgba(199, 210, 254, 0.86);
            }

            .title {
                margin: 0 0 1rem;
                font-family: 'Lora', serif;
                font-size: clamp(2rem, 4vw, 2.6rem);
                line-height: 1.1;
            }

            .description {
                margin: 0;
                font-size: 1rem;
                line-height: 1.9;
                color: var(--muted);
            }

            .notice {
                margin-top: 1.5rem;
                padding: 1rem 1.25rem;
                border-radius: 1.25rem;
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: rgba(0, 0, 0, 0.2);
                color: var(--soft);
                font-size: 0.95rem;
                line-height: 1.8;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.875rem;
                margin-top: 1.75rem;
            }

            .button,
            .link {
                appearance: none;
                border: 0;
                border-radius: 999px;
                padding: 0.9rem 1.25rem;
                font: inherit;
                text-decoration: none;
                transition: transform 120ms ease, background 120ms ease, border-color 120ms ease;
            }

            .button:hover,
            .link:hover {
                transform: translateY(-1px);
            }

            .button {
                background: var(--danger-soft);
                color: #fecdd3;
                border: 1px solid rgba(251, 113, 133, 0.28);
                cursor: pointer;
            }

            .link {
                background: var(--brand-soft);
                color: #c7d2fe;
                border: 1px solid rgba(129, 140, 248, 0.24);
            }

            .flash {
                margin-top: 1.25rem;
                padding: 0.9rem 1rem;
                border-radius: 1rem;
                border: 1px solid rgba(251, 191, 36, 0.22);
                background: rgba(245, 158, 11, 0.12);
                color: #fde68a;
                font-size: 0.95rem;
            }

            @media (max-width: 640px) {
                .card {
                    padding: 1.5rem;
                    border-radius: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        @php
            $maskedEmail = preg_replace('/(^.).*(@.*$)/', '$1***$2', $subscription->email) ?: $subscription->email;
        @endphp

        <main class="shell">
            <section class="card">
                <p class="eyebrow">Bülten Yönetimi</p>
                <h1 class="title">Abonelikten çıkmak istediğine emin misin?</h1>
                <p class="description">
                    {{ $maskedEmail }} adresi için {{ $site->site_name }} bülten aboneliğini sonlandırmak üzeresin.
                    Bu işlem geri alınabilir; daha sonra aynı e-posta ile tekrar abone olabilirsin.
                </p>

                <div class="notice">
                    Güvenlik için bağlantıya yalnızca ziyaret etmek aboneliği sonlandırmaz. İşlemi tamamlamak için aşağıdaki butona basman gerekir.
                </div>

                @if (session('error'))
                    <div class="flash">{{ session('error') }}</div>
                @endif

                <div class="actions">
                    <form method="POST" action="{{ route('newsletter.unsubscribe.destroy', $subscription->unsubscribe_token) }}">
                        @csrf
                        <button type="submit" class="button">Aboneliği Sonlandır</button>
                    </form>
                    <a href="{{ route('home') }}" class="link">Vazgeç ve Siteye Dön</a>
                </div>
            </section>
        </main>
    </body>
</html>
