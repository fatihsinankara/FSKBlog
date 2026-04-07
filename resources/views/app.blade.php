<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php($site = app(\App\Support\SiteSettings::class)->current())
    @php($themeStorageKey = 'fsk-color-mode')
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $site->default_meta_description ?: $site->site_description }}">
        @if($site->site_keywords)
            <meta name="keywords" content="{{ $site->site_keywords }}">
        @endif

        <title inertia>{{ $site->default_meta_title ?: $site->site_name }}</title>
        <link rel="icon" href="{{ $site->favicon_url ?: asset('favicon.ico') }}">
        <script nonce="{{ Vite::cspNonce() }}">
            (() => {
                const storageKey = @json($themeStorageKey);
                const root = document.documentElement;

                try {
                    const stored = localStorage.getItem(storageKey);
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const isDark = stored === 'dark' || ((stored === 'auto' || stored === null) && prefersDark);

                    root.classList.toggle('dark', isDark);
                    root.style.colorScheme = isDark ? 'dark' : 'light';
                } catch {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    root.classList.toggle('dark', prefersDark);
                    root.style.colorScheme = prefersDark ? 'dark' : 'light';
                }
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @routes(nonce: Vite::cspNonce())
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        @if($site->custom_head_code)
            {!! $site->custom_head_code !!}
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
        @if($site->custom_body_end_code)
            {!! $site->custom_body_end_code !!}
        @endif
    </body>
</html>
