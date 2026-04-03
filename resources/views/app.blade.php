<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php($site = app(\App\Support\SiteSettings::class)->current())
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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
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
