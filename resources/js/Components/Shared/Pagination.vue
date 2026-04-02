<script setup>
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    links: Array,
});

function visit(url) {
    if (!url) return;
    router.visit(url, { preserveScroll: false });
}
</script>

<template>
    <nav v-if="links && links.length > 3" class="flex items-center justify-center gap-1 mt-12" aria-label="Sayfalama">
        <template v-for="link in links" :key="link.label">
            <button
                v-if="link.label === '&laquo; Previous'"
                @click="visit(link.url)"
                :disabled="!link.url"
                class="p-2 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
            >
                <ChevronLeft :size="18" />
            </button>
            <button
                v-else-if="link.label === 'Next &raquo;'"
                @click="visit(link.url)"
                :disabled="!link.url"
                class="p-2 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800"
            >
                <ChevronRight :size="18" />
            </button>
            <button
                v-else
                @click="visit(link.url)"
                :disabled="!link.url"
                class="min-w-[36px] h-9 px-3 rounded-lg text-sm transition-colors"
                :class="link.active
                    ? 'bg-indigo-600 text-white font-medium'
                    : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed'"
            >
                {{ link.label }}
            </button>
        </template>
    </nav>
</template>
