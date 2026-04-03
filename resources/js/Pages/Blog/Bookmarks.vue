<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import { Bookmark } from 'lucide-vue-next';
import SiteHead from '@/Components/Shared/SiteHead.vue';

const props = defineProps({
    bookmarks: Object,
    filters: Object,
    statuses: Array,
});

function setFilter(status = '') {
    router.get(route('bookmarks.index'), status ? { status } : {}, {
        preserveState: true,
        preserveScroll: true,
    });
}

function updateStatus(bookmarkId, status) {
    router.patch(route('bookmarks.status', bookmarkId), { status }, { preserveScroll: true });
}
</script>

<template>
    <AppLayout>
        <SiteHead title="Kaydedilenler" description="Okumak için sakladığın yazılar." :canonical="route('bookmarks.index')" />

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

            <div class="mb-6 flex flex-wrap gap-2">
                <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-sm transition-colors"
                    :class="!filters?.status ? 'bg-indigo-600 text-white' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'"
                    @click="setFilter('')"
                >
                    Tümü
                </button>
                <button
                    v-for="status in statuses"
                    :key="status"
                    type="button"
                    class="rounded-full px-3 py-1.5 text-sm capitalize transition-colors"
                    :class="filters?.status === status ? 'bg-indigo-600 text-white' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'"
                    @click="setFilter(status)"
                >
                    {{ status }}
                </button>
            </div>

            <div v-if="bookmarks.data?.length">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="bookmark in bookmarks.data"
                        :key="bookmark.id"
                        class="space-y-3 rounded-[1.75rem] border border-neutral-200 bg-white p-3 dark:border-neutral-800 dark:bg-neutral-900"
                    >
                        <PostCard :post="bookmark.post" />
                        <div class="px-2 pb-1">
                            <label class="text-xs font-medium uppercase tracking-wide text-neutral-400">Durum</label>
                            <select
                                :value="bookmark.status"
                                class="mt-2 w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2 text-sm capitalize text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                @change="updateStatus(bookmark.id, $event.target.value)"
                            >
                                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>
                    </div>
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
