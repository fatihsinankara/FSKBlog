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

        <article class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
            <!-- Back link -->
            <Link :href="route('home')" class="inline-flex items-center gap-1.5 text-sm text-neutral-500 hover:text-neutral-900 dark:hover:text-white mb-8 transition-colors">
                <ArrowLeft :size="16" />
                Geri
            </Link>

            <!-- Header -->
            <header class="mb-10">
                <PostMeta :post="post" class="mb-4" />
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-neutral-900 dark:text-white leading-snug mb-4">
                    {{ post.title }}
                </h1>
                <p v-if="post.excerpt" class="text-lg text-neutral-600 dark:text-neutral-400 leading-relaxed mb-4">
                    {{ post.excerpt }}
                </p>
                <div v-if="post.tags?.length" class="flex flex-wrap gap-1.5">
                    <TagBadge v-for="tag in post.tags" :key="tag.id" :tag="tag" />
                </div>
            </header>

            <!-- Cover image -->
            <div v-if="post.featured_image_url" class="rounded-2xl overflow-hidden mb-10 aspect-video">
                <img
                    :src="post.featured_image_url"
                    :alt="post.featured_image_alt || post.title"
                    class="w-full h-full object-cover"
                />
            </div>

            <!-- Body -->
            <div
                class="prose prose-lg prose-neutral dark:prose-invert prose-headings:font-serif prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-code:before:content-none prose-code:after:content-none max-w-none"
                v-html="post.rendered_body"
            />

            <!-- Comments -->
            <CommentSection :post="post" :comments="post.comments || []" />
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
