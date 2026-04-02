<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Check, X } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    categories: Array,
});

const form = useForm({ name: '', description: '', color: '#6366f1' });
const editing = ref(null);
const editForm = useForm({ name: '', description: '', color: '#6366f1' });

function submit() {
    form.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function startEdit(cat) {
    editing.value = cat.id;
    editForm.name = cat.name;
    editForm.description = cat.description || '';
    editForm.color = cat.color;
}

function saveEdit(cat) {
    editForm.patch(route('admin.categories.update', cat.id), {
        preserveScroll: true,
        onSuccess: () => { editing.value = null; },
    });
}

function destroy(id) {
    if (confirm('Bu kategoriyi silmek istediğine emin misin?')) {
        router.delete(route('admin.categories.destroy', id), { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="Kategoriler" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kategoriler</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Add form -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4">Yeni Kategori</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <input v-model="form.name" type="text" placeholder="Kategori adı *" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" :class="{ 'border-red-400': form.errors.name }" />
                    <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>

                    <textarea v-model="form.description" rows="2" placeholder="Açıklama (isteğe bağlı)" class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" />

                    <div class="flex items-center gap-3">
                        <label class="text-xs text-neutral-500">Renk:</label>
                        <input v-model="form.color" type="color" class="w-8 h-8 rounded cursor-pointer border border-neutral-200 dark:border-neutral-700" />
                        <span class="text-xs font-mono text-neutral-500">{{ form.color }}</span>
                    </div>

                    <button type="submit" :disabled="form.processing" class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition-colors">
                        <Plus :size="16" />
                        Ekle
                    </button>
                </form>
            </div>

            <!-- List -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-800">
                    <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Mevcut Kategoriler</h2>
                </div>
                <div class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <div v-for="cat in categories" :key="cat.id" class="px-6 py-3">
                        <!-- Editing mode -->
                        <div v-if="editing === cat.id" class="space-y-2">
                            <input v-model="editForm.name" type="text" class="w-full px-2 py-1 text-sm rounded border border-indigo-400 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                            <div class="flex items-center gap-2">
                                <input v-model="editForm.color" type="color" class="w-7 h-7 rounded cursor-pointer border border-neutral-200" />
                                <div class="flex items-center gap-1 ml-auto">
                                    <button type="button" @click="saveEdit(cat)" class="p-1 text-green-600 hover:text-green-700 transition-colors">
                                        <Check :size="16" />
                                    </button>
                                    <button type="button" @click="editing = null" class="p-1 text-neutral-400 hover:text-neutral-700 transition-colors">
                                        <X :size="16" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- View mode -->
                        <div v-else class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: cat.color }" />
                                <div>
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ cat.name }}</p>
                                    <p class="text-xs text-neutral-400">{{ cat.posts_count }} yazı</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="startEdit(cat)" class="p-1 text-neutral-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    <Pencil :size="14" />
                                </button>
                                <button @click="destroy(cat.id)" class="p-1 text-neutral-400 hover:text-red-500 transition-colors">
                                    <Trash2 :size="14" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="!categories.length" class="px-6 py-8 text-center text-sm text-neutral-400">
                        Henüz kategori yok.
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
