<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FolderOpen } from 'lucide-vue-next';

defineProps({
    categories: Array,
});
</script>

<template>
    <AppLayout>
        <Head title="Kategoriler" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white mb-2">Kategoriler</h1>
            <p class="text-neutral-500 dark:text-neutral-400 mb-10">Konulara göre yazılara göz at.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="route('categories.show', cat.slug)"
                    class="group flex items-center gap-4 p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-neutral-300 dark:hover:border-neutral-700 transition-colors"
                >
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                        :style="{ backgroundColor: cat.color + '20', color: cat.color }"
                    >
                        <FolderOpen :size="20" />
                    </div>
                    <div>
                        <p class="font-semibold text-neutral-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ cat.name }}
                        </p>
                        <p class="text-xs text-neutral-400">{{ cat.posts_count }} yazı</p>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
