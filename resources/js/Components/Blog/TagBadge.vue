<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    tag: Object,
});
</script>

<template>
    <Link
        :href="route('tags.show', tag.slug)"
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors"
    >
        #{{ tag.name }}
    </Link>
</template>
