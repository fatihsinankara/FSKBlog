<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
    description: String,
    keywords: String,
    image: String,
    canonical: String,
    type: { type: String, default: 'website' },
    jsonLd: { type: [Object, Array], default: null },
    publishedAt: String,
});

const page = usePage();
const site = computed(() => page.props.site ?? {});

function absoluteUrl(value) {
    if (!value || typeof window === 'undefined') {
        return value ?? null;
    }

    if (value.startsWith('http://') || value.startsWith('https://')) {
        return value;
    }

    if (value.startsWith('//')) {
        return `${window.location.protocol}${value}`;
    }

    if (value.startsWith('/')) {
        return `${window.location.origin}${value}`;
    }

    return `${window.location.origin}/${value.replace(/^\/+/, '')}`;
}

const resolvedTitle = computed(() => {
    const siteName = site.value.site_name || 'FSK Blog';

    if (!props.title || props.title === siteName) {
        return siteName;
    }

    return `${props.title} | ${siteName}`;
});

const resolvedDescription = computed(() => props.description || site.value.default_meta_description || site.value.site_description || '');
const resolvedKeywords = computed(() => props.keywords || site.value.site_keywords || '');
const resolvedImage = computed(() => absoluteUrl(props.image || site.value.default_og_image_url));
const resolvedCanonical = computed(() => absoluteUrl(props.canonical || (typeof window !== 'undefined' ? window.location.href : '')));
const jsonLdList = computed(() => {
    if (!props.jsonLd) {
        return [];
    }

    return Array.isArray(props.jsonLd) ? props.jsonLd : [props.jsonLd];
});
</script>

<template>
    <Head>
        <title>{{ resolvedTitle }}</title>
        <meta v-if="resolvedDescription" name="description" :content="resolvedDescription" />
        <meta v-if="resolvedKeywords" name="keywords" :content="resolvedKeywords" />
        <meta property="og:site_name" :content="site.site_name || 'FSK Blog'" />
        <meta property="og:title" :content="resolvedTitle" />
        <meta v-if="resolvedDescription" property="og:description" :content="resolvedDescription" />
        <meta property="og:type" :content="type" />
        <meta v-if="resolvedCanonical" property="og:url" :content="resolvedCanonical" />
        <meta v-if="resolvedImage" property="og:image" :content="resolvedImage" />
        <meta name="twitter:card" :content="resolvedImage ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="resolvedTitle" />
        <meta v-if="resolvedDescription" name="twitter:description" :content="resolvedDescription" />
        <meta v-if="resolvedImage" name="twitter:image" :content="resolvedImage" />
        <meta v-if="publishedAt" property="article:published_time" :content="publishedAt" />
        <link v-if="resolvedCanonical" rel="canonical" :href="resolvedCanonical" />
        <link rel="alternate" type="application/rss+xml" :title="`${site.site_name || 'FSK Blog'} RSS`" :href="route('feed')" />
        <component
            v-for="(schema, index) in jsonLdList"
            :key="index"
            :is="'script'"
            type="application/ld+json"
            v-html="JSON.stringify(schema)"
        >
        </component>
    </Head>
</template>
