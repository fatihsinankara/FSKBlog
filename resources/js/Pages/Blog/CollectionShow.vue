<script setup>
import { computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';
import NewsletterSignup from '@/Components/Shared/NewsletterSignup.vue';
import { Bell, BellOff, ArrowRight, BookOpen } from 'lucide-vue-next';

const props = defineProps({
    collection: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

function toggleFollow() {
    router.post(route('collections.follow', props.collection.id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout>
        <SiteHead
            :title="collection.title"
            :description="collection.description || `${collection.title} serisinin tüm bölümleri`"
            :canonical="route('collections.show', collection.slug)"
            :json-ld="{
                '@context': 'https://schema.org',
                '@type': 'CollectionPage',
                name: collection.title,
                description: collection.description,
                url: route('collections.show', collection.slug),
            }"
        />

        <section class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <div class="rounded-[2rem] border border-neutral-200 bg-gradient-to-br from-white to-neutral-50 p-8 shadow-sm dark:border-neutral-800 dark:from-neutral-900 dark:to-neutral-950">
                <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            <BookOpen :size="14" />
                            Seri
                        </div>
                        <h1 class="mt-4 font-serif text-4xl font-semibold text-neutral-950 dark:text-white">{{ collection.title }}</h1>
                        <p v-if="collection.description" class="mt-4 text-base leading-8 text-neutral-600 dark:text-neutral-300">{{ collection.description }}</p>
                        <p class="mt-4 text-sm text-neutral-500 dark:text-neutral-400">
                            {{ collection.total_parts }} bölüm içerir.
                            <template v-if="collection.current_item">
                                Şu an kaldığın bölüm: <strong class="text-neutral-800 dark:text-neutral-200">{{ collection.current_item.part_number }}. bölüm</strong>.
                            </template>
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button
                            v-if="user"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium transition-colors"
                            :class="collection.is_following
                                ? 'border-indigo-300 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300'
                                : 'border-neutral-200 text-neutral-700 hover:border-neutral-300 dark:border-neutral-700 dark:text-neutral-300'"
                            @click="toggleFollow"
                        >
                            <BellOff v-if="collection.is_following" :size="15" />
                            <Bell v-else :size="15" />
                            {{ collection.is_following ? 'Takibi Bırak' : 'Seriyi Takip Et' }}
                        </button>
                        <Link
                            v-if="collection.current_item"
                            :href="route('posts.show', collection.current_item.slug)"
                            class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                        >
                            Seriye Git
                            <ArrowRight :size="15" />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <Link
                    v-for="item in collection.items"
                    :key="item.id"
                    :href="route('posts.show', item.slug)"
                    class="flex flex-col gap-4 rounded-[1.5rem] border border-neutral-200 bg-white p-5 transition hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700 md:flex-row md:items-center"
                    :class="collection.current_item?.id === item.id ? 'ring-2 ring-indigo-500/40' : ''"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-sm font-semibold text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-300">
                        {{ item.part_number }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-neutral-400">
                            <span v-if="item.category">{{ item.category.name }}</span>
                            <span v-if="item.published_at">{{ formatDate(item.published_at) }}</span>
                            <span>{{ item.reading_time_text }}</span>
                        </div>
                        <h2 class="mt-1 text-lg font-semibold text-neutral-950 dark:text-white">{{ item.title }}</h2>
                        <p v-if="item.excerpt" class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300">{{ item.excerpt }}</p>
                    </div>
                    <span v-if="collection.current_item?.id === item.id" class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">
                        Şu an
                    </span>
                </Link>
            </div>

            <NewsletterSignup title="Seri güncellemelerini al" :description="`${collection.title} serisine yeni bölüm eklendiğinde haber verelim.`" />
        </section>
    </AppLayout>
</template>
