<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SiteHead from '@/Components/Shared/SiteHead.vue';

defineProps({
    categories: Array,
});
</script>

<template>
    <AppLayout>
        <SiteHead
            title="Kategoriler"
            description="Konulara göre tüm yazıları keşfet."
            :canonical="route('categories.index')"
        />

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
                    <!-- Image or icon -->
                    <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0">
                        <img v-if="cat.image_url" :src="cat.image_url" class="w-full h-full object-cover" :alt="cat.name" />
                        <div v-else class="w-full h-full flex items-center justify-center"
                            :style="{ backgroundColor: cat.color + '20', color: cat.color }">
                            <font-awesome-icon v-if="cat.icon" :icon="['fas', cat.icon]" class="text-lg" />
                            <span v-else class="w-3 h-3 rounded-full block" :style="{ backgroundColor: cat.color }" />
                        </div>
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
