<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';
import { BookOpen, ArrowRight } from 'lucide-vue-next';

defineProps({
    collections: Array,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout>
        <SiteHead
            title="Seriler"
            description="Birbirini takip eden koleksiyonları ve yazı serilerini keşfet."
            :canonical="route('collections.index')"
        />

        <section class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="mb-10">
                <div class="inline-flex items-center gap-2 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                    <BookOpen :size="14" />
                    Koleksiyonlar
                </div>
                <h1 class="mt-4 text-3xl font-bold text-neutral-950 dark:text-white">Seriler</h1>
                <p class="mt-2 max-w-2xl text-neutral-500 dark:text-neutral-400">
                    Bölüm bölüm ilerleyen yazı serilerini tek yerden takip et. Her koleksiyonda başlangıç noktasını ve en güncel bölümü görebilirsin.
                </p>
            </div>

            <div v-if="collections.length" class="grid gap-6 lg:grid-cols-2">
                <article
                    v-for="collection in collections"
                    :key="collection.id"
                    class="overflow-hidden rounded-[1.75rem] border border-neutral-200 bg-white shadow-sm transition hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700"
                >
                    <Link
                        :href="route('collections.show', collection.slug)"
                        class="block"
                    >
                        <div v-if="collection.cover_item?.featured_image_url" class="aspect-[16/8.5] overflow-hidden bg-neutral-100 dark:bg-neutral-800">
                            <img
                                :src="collection.cover_item.featured_image_url"
                                :alt="collection.cover_item.featured_image_alt || collection.title"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between gap-4">
                                <h2 class="text-xl font-semibold text-neutral-950 dark:text-white">{{ collection.title }}</h2>
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-300">
                                    {{ collection.posts_count }} bölüm
                                </span>
                            </div>

                            <p v-if="collection.description" class="mt-3 text-sm leading-7 text-neutral-600 dark:text-neutral-300">
                                {{ collection.description }}
                            </p>

                            <div class="mt-5 rounded-2xl bg-neutral-50 px-4 py-4 dark:bg-neutral-800/60">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-neutral-400">En güncel bölüm</p>
                                <p v-if="collection.latest_item" class="mt-2 text-sm font-medium text-neutral-800 dark:text-neutral-100">
                                    {{ collection.latest_item.part_number }}. {{ collection.latest_item.title }}
                                </p>
                                <p v-if="collection.latest_item?.published_at" class="mt-1 text-xs text-neutral-400">
                                    {{ formatDate(collection.latest_item.published_at) }}
                                </p>
                            </div>

                            <div class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                Seriyi aç
                                <ArrowRight :size="15" />
                            </div>
                        </div>
                    </Link>
                </article>
            </div>

            <div v-else class="rounded-[1.75rem] border border-neutral-200 bg-white px-6 py-12 text-center text-neutral-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400">
                Henüz yayınlanmış koleksiyon yok.
            </div>
        </section>
    </AppLayout>
</template>
