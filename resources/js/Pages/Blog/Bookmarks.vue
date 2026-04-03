<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import { Bookmark } from 'lucide-vue-next';

defineProps({
    bookmarks: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Kaydedilenler" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-2">
                    <Bookmark :size="22" class="text-indigo-500" />
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kaydedilenler</h1>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Okumak için kaydettiğin yazılar.
                </p>
            </div>

            <div v-if="bookmarks.data?.length">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <PostCard
                        v-for="bookmark in bookmarks.data"
                        :key="bookmark.id"
                        :post="bookmark.post"
                    />
                </div>
                <Pagination :links="bookmarks.links" />
            </div>

            <div v-else class="py-24 text-center">
                <Bookmark :size="40" class="text-neutral-300 dark:text-neutral-700 mx-auto mb-4" />
                <p class="text-neutral-500 dark:text-neutral-400">Henüz kaydettiğin yazı yok.</p>
                <Link :href="route('home')" class="mt-4 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    Yazılara göz at
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
