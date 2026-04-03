<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ $site->site_name }}</title>
        <link>{{ $siteUrl }}</link>
        <description>{{ $site->site_description }}</description>
        <language>tr</language>
        <atom:link href="{{ $siteUrl }}/feed.xml" rel="self" type="application/rss+xml" />
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>

        @foreach($posts as $post)
        <item>
            <title><![CDATA[{{ $post['title'] }}]]></title>
            <link>{{ $siteUrl }}/posts/{{ $post['slug'] }}</link>
            <guid isPermaLink="true">{{ $siteUrl }}/posts/{{ $post['slug'] }}</guid>
            <pubDate>{{ \Carbon\Carbon::parse($post['published_at'])->toRfc2822String() }}</pubDate>
            @if(!empty($post['category']))
            <category><![CDATA[{{ $post['category']['name'] }}]]></category>
            @endif
            <description><![CDATA[{{ $post['excerpt'] ?? '' }}]]></description>
        </item>
        @endforeach
    </channel>
</rss>
