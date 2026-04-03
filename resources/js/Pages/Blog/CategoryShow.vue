<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';

defineProps({
    category: Object,
    posts: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="category.name" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10 flex items-center gap-5">
                <!-- Image or icon -->
                <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0 shadow-sm">
                    <img v-if="category.image_url" :src="category.image_url" class="w-full h-full object-cover" :alt="category.name" />
                    <div v-else class="w-full h-full flex items-center justify-center"
                        :style="{ backgroundColor: category.color + '20', color: category.color }">
                        <font-awesome-icon v-if="category.icon" :icon="['fas', category.icon]" class="text-2xl" />
                        <span v-else class="w-4 h-4 rounded-full block" :style="{ backgroundColor: category.color }" />
                    </div>
                </div>
                <div>
                    <div
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mb-2"
                        :style="{ backgroundColor: category.color + '20', color: category.color }"
                    >
                        Kategori
                    </div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">{{ category.name }}</h1>
                    <p v-if="category.description" class="text-neutral-500 dark:text-neutral-400 mt-1">{{ category.description }}</p>
                </div>
            </div>

            <div v-if="posts.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
            </div>
            <p v-else class="text-neutral-400 py-12 text-center">Bu kategoride henüz yazı yok.</p>

            <Pagination :links="posts.links" />
        </div>
    </AppLayout>
</template>
