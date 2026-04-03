<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';

defineProps({
    tags: Array,
});

function destroy(id) {
    if (confirm('Bu tagi silmek istediğine emin misin?')) {
        router.delete(route('admin.tags.destroy', id));
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Taglar" />

        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Taglar</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Etiketleri tek listede gör, düzenle ve yazı kullanım sayılarını takip et.
                </p>
            </div>

            <Link
                :href="route('admin.tags.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
            >
                <Plus :size="16" />
                Yeni Tag
            </Link>
        </div>

        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div v-if="tags.length" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <div
                    v-for="tag in tags"
                    :key="tag.id"
                    class="flex items-center justify-between px-6 py-4"
                >
                    <div>
                        <p class="text-sm font-medium text-neutral-900 dark:text-white">#{{ tag.name }}</p>
                        <p class="mt-0.5 text-xs text-neutral-400">{{ tag.posts_count }} yazı</p>
                    </div>

                    <div class="flex items-center gap-1">
                        <Link :href="route('admin.tags.edit', tag.id)" class="p-2 text-neutral-400 transition-colors hover:text-indigo-600 dark:hover:text-indigo-400">
                            <Pencil :size="15" />
                        </Link>
                        <button @click="destroy(tag.id)" class="p-2 text-neutral-400 transition-colors hover:text-red-500">
                            <Trash2 :size="15" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="px-6 py-12 text-center text-sm text-neutral-400">Henüz tag yok.</div>
        </div>
    </AdminLayout>
</template>
