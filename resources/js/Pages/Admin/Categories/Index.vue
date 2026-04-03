<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';

defineProps({
    categories: Array,
});

function destroy(id) {
    if (confirm('Bu kategoriyi silmek istediğine emin misin?')) {
        router.delete(route('admin.categories.destroy', id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Kategoriler" />

        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kategoriler</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Blog kategorilerini yönet, görsellerini güncelle ve yazı sayılarını takip et.
                </p>
            </div>

            <Link
                :href="route('admin.categories.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
            >
                <Plus :size="16" />
                Yeni Kategori
            </Link>
        </div>

        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <div v-for="category in categories" :key="category.id" class="px-5 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="h-10 w-10 overflow-hidden rounded-xl shrink-0">
                                <img v-if="category.image_url" :src="category.image_url" class="h-full w-full object-cover" />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                    :style="{ backgroundColor: `${category.color}20`, color: category.color }"
                                >
                                    <font-awesome-icon v-if="category.icon" :icon="['fas', category.icon]" class="text-sm" />
                                    <div v-else class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: category.color }" />
                                </div>
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-neutral-900 dark:text-white">{{ category.name }}</p>
                                <p class="mt-0.5 text-xs text-neutral-400">{{ category.posts_count }} yazı</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            <Link :href="route('admin.categories.edit', category.id)" class="p-2 text-neutral-400 transition-colors hover:text-indigo-600 dark:hover:text-indigo-400">
                                <Pencil :size="15" />
                            </Link>
                            <button @click="destroy(category.id)" class="p-2 text-neutral-400 transition-colors hover:text-red-500">
                                <Trash2 :size="15" />
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="!categories.length" class="px-6 py-12 text-center text-sm text-neutral-400">
                    Henüz kategori yok.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
