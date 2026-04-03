<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PostCard from '@/Components/Blog/PostCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';
import { Search, SlidersHorizontal, X } from 'lucide-vue-next';

const props = defineProps({
    posts: Object,
    query: String,
    filters: Object,
    categories: Array,
    tags: Array,
    collections: Array,
    years: Array,
});

const form = reactive({
    q: props.filters?.q || '',
    category: props.filters?.category || '',
    tag: props.filters?.tag || '',
    collection: props.filters?.collection || '',
    year: props.filters?.year || '',
    reading_time: props.filters?.reading_time || '',
    sort: props.filters?.sort || 'latest',
});

function cleanPayload() {
    return Object.fromEntries(Object.entries(form).filter(([, value]) => value !== '' && value !== null));
}

function submit() {
    router.get(route('search'), cleanPayload(), {
        preserveState: true,
        preserveScroll: true,
    });
}

function clearFilters() {
    Object.assign(form, {
        q: '',
        category: '',
        tag: '',
        collection: '',
        year: '',
        reading_time: '',
        sort: 'latest',
    });

    submit();
}
</script>

<template>
    <AppLayout>
        <SiteHead
            :title="query ? `${query} için arama` : 'İçerik Keşfi'"
            :description="query ? `${query} araması için sonuçlar` : 'Tüm yazılarda kategori, etiket, seri ve yıl bazlı filtreleme yap.'"
            :canonical="route('search')"
        />

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

            <form class="mb-10 rounded-[1.75rem] border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900" @submit.prevent="submit">
                <div class="mb-4 flex items-center gap-2 text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                    <SlidersHorizontal :size="16" />
                    Filtreler
                </div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <input v-model="form.q" type="text" placeholder="Kelime ara..." class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white" />
                    <select v-model="form.category" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Tüm kategoriler</option>
                        <option v-for="category in categories" :key="category.id" :value="category.slug">{{ category.name }}</option>
                    </select>
                    <select v-model="form.tag" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Tüm tagler</option>
                        <option v-for="tag in tags" :key="tag.id" :value="tag.slug">#{{ tag.name }}</option>
                    </select>
                    <select v-model="form.collection" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Tüm seriler</option>
                        <option v-for="collection in collections" :key="collection.id" :value="collection.slug">{{ collection.title }}</option>
                    </select>
                    <select v-model="form.year" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Tüm yıllar</option>
                        <option v-for="year in years" :key="year" :value="String(year)">{{ year }}</option>
                    </select>
                    <select v-model="form.reading_time" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Tüm okuma süreleri</option>
                        <option value="short">0-5 dk</option>
                        <option value="medium">6-10 dk</option>
                        <option value="long">11+ dk</option>
                    </select>
                    <select v-model="form.sort" class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="latest">En yeni</option>
                        <option value="oldest">En eski</option>
                        <option value="most_viewed">En çok okunan</option>
                    </select>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                        <Search :size="14" />
                        Uygula
                    </button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 transition hover:border-neutral-300 dark:border-neutral-700 dark:text-neutral-300" @click="clearFilters">
                        <X :size="14" />
                        Temizle
                    </button>
                </div>
            </form>

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
