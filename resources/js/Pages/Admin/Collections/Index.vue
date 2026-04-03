<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, FolderTree } from 'lucide-vue-next';

defineProps({
    collections: Object,
});

function destroyCollection(id) {
    if (!window.confirm('Bu koleksiyon silinsin mi?')) {
        return;
    }

    router.delete(route('admin.collections.destroy', id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="Koleksiyonlar" />

        <div class="flex flex-col gap-3 mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Koleksiyonlar</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                    Serileri bir araya getir, part sirasini belirle ve yazilar arasinda akici gecis kur.
                </p>
            </div>

            <Link
                :href="route('admin.collections.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
            >
                <Plus :size="16" />
                Yeni Koleksiyon
            </Link>
        </div>

        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div v-if="collections.data.length" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <div
                    v-for="collection in collections.data"
                    :key="collection.id"
                    class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                                <FolderTree :size="18" />
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-neutral-900 dark:text-white">
                                    {{ collection.title }}
                                </p>
                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ collection.posts_count }} part
                                </p>
                            </div>
                        </div>
                        <p v-if="collection.description" class="mt-3 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-300">
                            {{ collection.description }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="collection.status === 'published'
                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'"
                        >
                            {{ collection.status === 'published' ? 'Yayinda' : 'Taslak' }}
                        </span>

                        <Link
                            :href="route('admin.collections.edit', collection.id)"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-indigo-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-indigo-400"
                        >
                            <Pencil :size="15" />
                            Duzenle
                        </Link>

                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-950/40"
                            @click="destroyCollection(collection.id)"
                        >
                            <Trash2 :size="15" />
                            Sil
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <p class="text-base font-medium text-neutral-900 dark:text-white">Henuz koleksiyon yok.</p>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                    Ilk serini olusturup mevcut yazilari part numaralariyla birbirine baglayabilirsin.
                </p>
            </div>
        </div>

        <Pagination :links="collections.links" />
    </AdminLayout>
</template>
