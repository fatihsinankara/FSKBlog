<script setup>
import { Link } from '@inertiajs/vue3';
import { Clock, Calendar } from 'lucide-vue-next';

defineProps({
    post: Object,
    showCategory: { type: Boolean, default: true },
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', {
        year: 'numeric', month: 'long', day: 'numeric',
    });
}
</script>

<template>
    <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-[0.72rem] uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">
        <Link
            v-if="showCategory && post.category"
            :href="route('categories.show', post.category.slug)"
            class="inline-flex items-center gap-2 font-semibold transition-colors"
        >
            <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: post.category.color }" />
            <span :style="{ color: post.category.color }">{{ post.category.name }}</span>
        </Link>
        <span v-if="post.published_at" class="flex items-center gap-1.5">
            <Calendar :size="12" />
            {{ formatDate(post.published_at) }}
        </span>
        <span class="flex items-center gap-1.5">
            <Clock :size="12" />
            {{ post.reading_time_text || post.reading_time + ' dk' }}
        </span>
    </div>
</template>
