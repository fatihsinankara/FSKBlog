<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import PostMeta from '@/Components/Blog/PostMeta.vue';
import TagBadge from '@/Components/Blog/TagBadge.vue';
import CommentSection from '@/Components/Blog/CommentSection.vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    post: Object,
    related: Array,
});
</script>

<template>
    <AppLayout>
        <Head>
            <title>{{ post.meta_title || post.title }} | FSK Blog</title>
            <meta name="description" :content="post.meta_description || post.excerpt" />
            <meta property="og:title" :content="post.meta_title || post.title" />
            <meta property="og:description" :content="post.meta_description || post.excerpt" />
            <meta v-if="post.featured_image_url" property="og:image" :content="post.featured_image_url" />
        </Head>

        <article class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            <!-- Back link -->
            <Link :href="route('home')" class="inline-flex items-center gap-1.5 text-sm text-neutral-500 hover:text-neutral-900 dark:hover:text-white mb-8 transition-colors">
                <ArrowLeft :size="16" />
                Geri
            </Link>

            <header class="max-w-3xl mx-auto mb-10 sm:mb-12">
                <PostMeta :post="post" class="mb-5" />
                <h1 class="font-serif text-[2rem] sm:text-[3rem] lg:text-[3.35rem] font-semibold text-neutral-950 dark:text-white leading-[1.02] mb-5 text-balance">
                    {{ post.title }}
                </h1>
                <p v-if="post.excerpt" class="text-[1.05rem] sm:text-[1.16rem] text-neutral-600 dark:text-neutral-300 leading-8 max-w-2xl">
                    {{ post.excerpt }}
                </p>
                <div v-if="post.tags?.length" class="flex flex-wrap gap-1.5 mt-6">
                    <TagBadge v-for="tag in post.tags" :key="tag.id" :tag="tag" />
                </div>
            </header>

            <div v-if="post.featured_image_url" class="mb-10 sm:mb-14">
                <div class="relative rounded-[2rem] overflow-hidden aspect-[16/8.7] sm:aspect-[16/8] ring-1 ring-black/5 dark:ring-white/5 bg-neutral-100 dark:bg-neutral-900">
                    <img
                        :src="post.featured_image_url"
                        :alt="post.featured_image_alt || post.title"
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-white/10 dark:from-black/20 dark:to-white/5 pointer-events-none" />
                </div>
            </div>

            <div class="max-w-2xl mx-auto relative">
                <div class="hidden sm:block absolute -left-10 top-2 h-24 w-px bg-gradient-to-b from-transparent via-neutral-300 to-transparent dark:via-neutral-700" />
                <div
                    class="blog-prose prose prose-neutral dark:prose-invert prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-code:before:content-none prose-code:after:content-none max-w-none"
                    v-html="post.rendered_body"
                />
            </div>

            <!-- Comments -->
            <CommentSection :post="post" />
        </article>

        <!-- Related posts -->
        <div v-if="related.length" class="max-w-5xl mx-auto px-4 sm:px-6 pb-16">
            <div class="border-t border-neutral-200 dark:border-neutral-800 pt-12">
                <h2 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-6">
                    İlgili Yazılar
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <PostCard v-for="p in related" :key="p.id" :post="p" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
