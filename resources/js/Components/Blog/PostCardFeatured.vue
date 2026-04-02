<script setup>
import { Link } from '@inertiajs/vue3';
import PostMeta from './PostMeta.vue';
import TagBadge from './TagBadge.vue';

defineProps({
    post: Object,
});
</script>

<template>
    <article class="group relative rounded-2xl overflow-hidden bg-neutral-900 dark:bg-neutral-800 min-h-[420px] flex items-end">
        <img
            v-if="post.featured_image_url"
            :src="post.featured_image_url"
            :alt="post.featured_image_alt || post.title"
            class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-70 group-hover:scale-105 transition-all duration-500"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />

        <div class="relative p-8 text-white w-full">
            <div class="mb-3">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/80 backdrop-blur-sm mb-3">
                    Öne Çıkan
                </span>
                <PostMeta :post="post" class="text-white/70" />
            </div>

            <Link :href="route('posts.show', post.slug)">
                <h2 class="text-2xl md:text-3xl font-bold mb-3 group-hover:text-indigo-300 transition-colors">
                    {{ post.title }}
                </h2>
            </Link>

            <p v-if="post.excerpt" class="text-white/70 text-sm line-clamp-2 max-w-2xl mb-4">
                {{ post.excerpt }}
            </p>

            <div v-if="post.tags?.length" class="flex flex-wrap gap-1">
                <TagBadge v-for="tag in post.tags" :key="tag.id" :tag="tag" />
            </div>
        </div>
    </article>
</template>
