<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';

const props = defineProps({
    page: Object,
});

const metaTitle       = props.page.meta_title       || props.page.title;
const metaDescription = props.page.meta_description || '';
</script>

<template>
    <AppLayout>
        <SiteHead
            :title="metaTitle"
            :description="metaDescription"
            :canonical="route('pages.show', page.slug)"
            :json-ld="{
                '@context': 'https://schema.org',
                '@type': 'WebPage',
                name: page.title,
                url: route('pages.show', page.slug),
            }"
        />

        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
            <article>
                <header class="mb-10">
                    <h1 class="text-3xl sm:text-4xl font-bold text-neutral-900 dark:text-white leading-tight">
                        {{ page.title }}
                    </h1>
                </header>

                <div
                    class="blog-prose prose prose-neutral dark:prose-invert max-w-none
                           prose-headings:font-semibold prose-headings:tracking-tight
                           prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-a:no-underline hover:prose-a:underline
                           prose-code:text-indigo-600 dark:prose-code:text-indigo-400
                           prose-code:before:content-none prose-code:after:content-none
                           prose-pre:bg-neutral-950 prose-pre:border prose-pre:border-neutral-800"
                    v-html="page.rendered_body"
                />
            </article>
        </div>
    </AppLayout>
</template>
