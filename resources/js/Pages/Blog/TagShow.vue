<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';

defineProps({
    tag: Object,
    posts: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="`#${tag.name}`" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 mb-3">
                    Tag
                </div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">#{{ tag.name }}</h1>
            </div>

            <div v-if="posts.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
            </div>
            <p v-else class="text-neutral-400 py-12 text-center">Bu tag'e sahip yazı yok.</p>

            <Pagination :links="posts.links" />
        </div>
    </AppLayout>
</template>
