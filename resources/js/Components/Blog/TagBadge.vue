<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    tag: Object,
});
</script>

<template>
    <Link
        :href="route('tags.show', tag.slug)"
        class="inline-flex items-center px-3 py-1 rounded-full text-[0.72rem] font-semibold uppercase tracking-[0.14em] bg-neutral-100/90 dark:bg-neutral-800/90 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors"
    >
        #{{ tag.name }}
    </Link>
</template>
