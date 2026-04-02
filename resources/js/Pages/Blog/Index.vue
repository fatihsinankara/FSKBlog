<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import PostCardFeatured from '@/Components/Blog/PostCardFeatured.vue';
import Pagination from '@/Components/Shared/Pagination.vue';

defineProps({
    featured: Object,
    posts: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Anasayfa" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <!-- Featured post -->
            <div v-if="featured" class="mb-12">
                <PostCardFeatured :post="featured" />
            </div>

            <!-- Posts grid -->
            <div v-if="posts.data.length">
                <h2 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-widest mb-6">
                    Son Yazılar
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
                </div>
                <Pagination :links="posts.links" />
            </div>

            <div v-else class="py-24 text-center text-neutral-400">
                <p class="text-lg">Henüz yazı yok.</p>
            </div>
        </div>
    </AppLayout>
</template>
