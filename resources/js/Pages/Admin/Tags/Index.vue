<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';

defineProps({
    tags: Array,
});

const form = useForm({ name: '' });

function submit() {
    form.post(route('admin.tags.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function destroy(id) {
    if (confirm('Bu tagi silmek istediğine emin misin?')) {
        router.delete(route('admin.tags.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Taglar" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Taglar</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4">Yeni Tag</h2>
                <form @submit.prevent="submit" class="flex gap-2">
                    <input v-model="form.name" type="text" placeholder="Tag adı *" class="flex-1 px-3 py-2 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" :class="{ 'border-red-400': form.errors.name }" />
                    <button type="submit" :disabled="form.processing" class="flex items-center gap-1 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition-colors">
                        <Plus :size="16" />
                        Ekle
                    </button>
                </form>
                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
            </div>

            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4">Mevcut Taglar</h2>
                <div v-if="tags.length" class="flex flex-wrap gap-2">
                    <div
                        v-for="tag in tags"
                        :key="tag.id"
                        class="flex items-center gap-1 px-3 py-1 rounded-full text-sm bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300"
                    >
                        #{{ tag.name }}
                        <span class="text-xs text-neutral-400">({{ tag.posts_count }})</span>
                        <button @click="destroy(tag.id)" class="ml-1 text-neutral-400 hover:text-red-500 transition-colors">
                            <Trash2 :size="12" />
                        </button>
                    </div>
                </div>
                <p v-else class="text-sm text-neutral-400">Henüz tag yok.</p>
            </div>
        </div>
    </AdminLayout>
</template>
