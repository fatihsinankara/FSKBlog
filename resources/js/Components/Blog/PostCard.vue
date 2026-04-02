<script setup>
import { Link } from '@inertiajs/vue3';
import PostMeta from './PostMeta.vue';
import TagBadge from './TagBadge.vue';

defineProps({
    post: Object,
});
</script>

<template>
    <article class="group flex flex-col bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden hover:border-neutral-300 dark:hover:border-neutral-700 transition-colors">
        <Link v-if="post.featured_image_url" :href="route('posts.show', post.slug)" class="block overflow-hidden aspect-video">
            <img
                :src="post.featured_image_url"
                :alt="post.featured_image_alt || post.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
            />
        </Link>

        <div class="flex flex-col flex-1 p-5">
            <PostMeta :post="post" class="mb-3" />

            <Link :href="route('posts.show', post.slug)">
                <h2 class="font-semibold text-neutral-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                    {{ post.title }}
                </h2>
            </Link>

            <p v-if="post.excerpt" class="text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2 flex-1">
                {{ post.excerpt }}
            </p>

            <div v-if="post.tags?.length" class="flex flex-wrap gap-1 mt-3">
                <TagBadge v-for="tag in post.tags" :key="tag.id" :tag="tag" />
            </div>
        </div>
    </article>
</template>
