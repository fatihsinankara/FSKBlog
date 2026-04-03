<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronDown, ChevronUp, ArrowLeft, ArrowRight, ListTree } from 'lucide-vue-next';

const props = defineProps({
    collection: Object,
});

const expanded = ref(false);

const visibleItems = computed(() => {
    if (!props.collection?.items?.length) {
        return [];
    }

    if (expanded.value || props.collection.items.length <= 5) {
        return props.collection.items;
    }

    const currentIndex = props.collection.items.findIndex((item) => item.is_current);

    if (currentIndex < 5) {
        return props.collection.items.slice(0, 5);
    }

    return [
        ...props.collection.items.slice(0, 4),
        props.collection.items[currentIndex],
    ];
});

const hiddenCount = computed(() => {
    if (!props.collection?.items?.length) {
        return 0;
    }

    return Math.max(props.collection.items.length - 5, 0);
});
</script>

<template>
    <section
        v-if="collection"
        class="mx-auto mb-10 max-w-3xl rounded-[1.75rem] border border-neutral-200 bg-gradient-to-br from-white to-neutral-50 p-6 shadow-sm dark:border-neutral-800 dark:from-neutral-900 dark:to-neutral-950"
    >
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-xl">
                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-neutral-100 px-3 py-1 text-[0.72rem] font-semibold uppercase tracking-[0.22em] text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                    <ListTree :size="14" />
                    Koleksiyon
                </div>
                <Link :href="route('collections.show', { slug: collection.slug, current: collection.current_slug })" class="inline-block">
                    <h2 class="font-serif text-2xl font-semibold text-neutral-950 transition-colors hover:text-indigo-600 dark:text-white dark:hover:text-indigo-300">
                        {{ collection.title }}
                    </h2>
                </Link>
                <p class="mt-3 text-sm leading-7 text-neutral-600 dark:text-neutral-300">
                    Bu yazi bu serinin <strong class="font-semibold text-neutral-900 dark:text-white">{{ collection.current_part }}.</strong> parcasi.
                    Toplamda {{ collection.total_parts }} parcadan olusuyor.
                </p>
                <p v-if="collection.description" class="mt-3 text-sm leading-7 text-neutral-500 dark:text-neutral-400">
                    {{ collection.description }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3 lg:justify-end">
                <Link
                    :href="route('collections.show', { slug: collection.slug, current: collection.current_slug })"
                    class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 px-3 py-2 text-sm text-neutral-700 transition-colors hover:border-neutral-300 hover:text-neutral-950 dark:border-neutral-700 dark:text-neutral-300 dark:hover:border-neutral-600 dark:hover:text-white"
                >
                    <ListTree :size="15" />
                    Seriye Git
                </Link>
                <Link
                    v-if="collection.previous"
                    :href="route('posts.show', collection.previous.slug)"
                    class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 px-3 py-2 text-sm text-neutral-700 transition-colors hover:border-neutral-300 hover:text-neutral-950 dark:border-neutral-700 dark:text-neutral-300 dark:hover:border-neutral-600 dark:hover:text-white"
                >
                    <ArrowLeft :size="15" />
                    Onceki Part
                </Link>
                <Link
                    v-if="collection.next"
                    :href="route('posts.show', collection.next.slug)"
                    class="inline-flex items-center gap-2 rounded-xl bg-neutral-900 px-3 py-2 text-sm text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                >
                    Sonraki Part
                    <ArrowRight :size="15" />
                </Link>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-neutral-200/80 bg-white/70 p-4 dark:border-neutral-800 dark:bg-neutral-900/70">
            <ol class="space-y-2">
                <li v-for="item in visibleItems" :key="item.id">
                    <Link
                        :href="route('posts.show', item.slug)"
                        class="flex items-center justify-between gap-4 rounded-xl px-3 py-2 text-sm transition-colors"
                        :class="item.is_current
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300'
                            : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800'"
                    >
                        <span class="min-w-0 truncate">
                            <span class="mr-2 font-semibold opacity-70">{{ item.part_number }}.</span>
                            {{ item.title }}
                        </span>
                        <span
                            v-if="item.is_current"
                            class="shrink-0 rounded-full bg-white px-2 py-0.5 text-[0.7rem] font-semibold uppercase tracking-[0.16em] text-indigo-600 dark:bg-neutral-900 dark:text-indigo-300"
                        >
                            Su an
                        </span>
                    </Link>
                </li>
            </ol>

            <button
                v-if="hiddenCount > 0"
                type="button"
                class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-neutral-600 transition-colors hover:text-neutral-950 dark:text-neutral-300 dark:hover:text-white"
                @click="expanded = !expanded"
            >
                <component :is="expanded ? ChevronUp : ChevronDown" :size="16" />
                {{ expanded ? 'Daha az goster' : `${hiddenCount} part daha goster` }}
            </button>
        </div>
    </section>
</template>
