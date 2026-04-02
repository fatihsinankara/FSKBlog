<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import { Search } from 'lucide-vue-next';

defineProps({
    posts: Object,
    query: String,
});
</script>

<template>
    <AppLayout>
        <Head :title="query ? query + ' için arama' : 'Arama'" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10">
                <div class="flex items-center gap-2 text-neutral-400 mb-2">
                    <Search :size="16" />
                    <span class="text-sm">Arama sonuçları</span>
                </div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                    <template v-if="query">"{{ query }}"</template>
                    <template v-else>Tüm yazılar</template>
                </h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ posts.total }} sonuç bulundu
                </p>
            </div>

            <div v-if="posts.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
            </div>
            <div v-else class="py-24 text-center">
                <Search :size="48" class="mx-auto mb-4 text-neutral-200 dark:text-neutral-700" />
                <p class="text-neutral-500">Sonuç bulunamadı.</p>
            </div>

            <Pagination :links="posts.links" />
        </div>
    </AppLayout>
</template>
