<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Check, X, ImageOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    categories: Array,
});

const form = useForm({
    name: '',
    description: '',
    color: '#6366f1',
    icon: '',
    image: null,
});

const editing = ref(null);
const editForm = useForm({
    name: '',
    description: '',
    color: '#6366f1',
    icon: '',
    image: null,
    remove_image: false,
});
const editImagePreview = ref(null);
const createImagePreview = ref(null);

function onCreateImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.image = file;
    createImagePreview.value = URL.createObjectURL(file);
}

function onEditImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    editForm.image = file;
    editForm.remove_image = false;
    editImagePreview.value = URL.createObjectURL(file);
}

function submit() {
    form.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            createImagePreview.value = null;
        },
    });
}

function startEdit(cat) {
    editing.value = cat.id;
    editForm.name = cat.name;
    editForm.description = cat.description || '';
    editForm.color = cat.color;
    editForm.icon = cat.icon || '';
    editForm.image = null;
    editForm.remove_image = false;
    editImagePreview.value = null;
}

function saveEdit(cat) {
    editForm.post(route('admin.categories.update', cat.id), {
        preserveScroll: true,
        data: { _method: 'PATCH' },
        forceFormData: true,
        onSuccess: () => {
            editing.value = null;
            editImagePreview.value = null;
        },
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
                    <input v-model="form.name" type="text" placeholder="Kategori adı *"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :class="{ 'border-red-400': form.errors.name }" />
                    <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>

                    <textarea v-model="form.description" rows="2" placeholder="Açıklama (isteğe bağlı)"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" />

                    <div class="flex items-center gap-3">
                        <label class="text-xs text-neutral-500 w-14 shrink-0">Renk:</label>
                        <input v-model="form.color" type="color" class="w-8 h-8 rounded cursor-pointer border border-neutral-200 dark:border-neutral-700" />
                        <span class="text-xs font-mono text-neutral-500">{{ form.color }}</span>
                    </div>

                    <!-- Icon -->
                    <div class="flex items-center gap-3">
                        <label class="text-xs text-neutral-500 w-14 shrink-0">FA İkon:</label>
                        <input v-model="form.icon" type="text" placeholder="ör: code, laptop, pen-nib"
                            class="flex-1 px-3 py-1.5 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        <div v-if="form.icon" class="w-8 h-8 rounded-lg flex items-center justify-center"
                            :style="{ backgroundColor: form.color + '20', color: form.color }">
                            <font-awesome-icon :icon="['fas', form.icon]" />
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="space-y-1.5">
                        <label class="text-xs text-neutral-500">Görsel:</label>
                        <div class="flex items-center gap-3">
                            <img v-if="createImagePreview" :src="createImagePreview" class="w-12 h-12 rounded-lg object-cover border border-neutral-200 dark:border-neutral-700" />
                            <label class="cursor-pointer px-3 py-1.5 text-xs rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors">
                                Görsel Seç
                                <input type="file" accept="image/*" class="hidden" @change="onCreateImage" />
                            </label>
                        </div>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition-colors">
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
                    <div v-for="cat in categories" :key="cat.id" class="px-5 py-3">

                        <!-- Editing mode -->
                        <div v-if="editing === cat.id" class="space-y-2">
                            <input v-model="editForm.name" type="text"
                                class="w-full px-2 py-1 text-sm rounded border border-indigo-400 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                            <textarea v-model="editForm.description" rows="2" placeholder="Açıklama"
                                class="w-full px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none resize-none" />
                            <div class="flex items-center gap-2">
                                <input v-model="editForm.color" type="color" class="w-7 h-7 rounded cursor-pointer border border-neutral-200" />
                                <input v-model="editForm.icon" type="text" placeholder="FA ikon adı"
                                    class="flex-1 px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none" />
                                <div v-if="editForm.icon" class="w-7 h-7 rounded flex items-center justify-center shrink-0"
                                    :style="{ backgroundColor: editForm.color + '20', color: editForm.color }">
                                    <font-awesome-icon :icon="['fas', editForm.icon]" class="text-xs" />
                                </div>
                            </div>

                            <!-- Image edit -->
                            <div class="flex items-center gap-2">
                                <img v-if="editImagePreview" :src="editImagePreview" class="w-9 h-9 rounded-lg object-cover border border-neutral-200 dark:border-neutral-700" />
                                <img v-else-if="cat.image_url && !editForm.remove_image" :src="cat.image_url" class="w-9 h-9 rounded-lg object-cover border border-neutral-200 dark:border-neutral-700" />
                                <label class="cursor-pointer px-2 py-1 text-xs rounded border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 transition-colors">
                                    Görsel
                                    <input type="file" accept="image/*" class="hidden" @change="onEditImage" />
                                </label>
                                <button v-if="cat.image_url && !editForm.remove_image && !editImagePreview" type="button"
                                    @click="editForm.remove_image = true"
                                    class="p-1 text-red-400 hover:text-red-600 transition-colors" title="Görseli kaldır">
                                    <ImageOff :size="14" />
                                </button>
                                <span v-if="editForm.remove_image" class="text-xs text-red-500">Görsel silinecek</span>
                            </div>

                            <div class="flex items-center justify-end gap-1">
                                <button type="button" @click="saveEdit(cat)" class="p-1 text-green-600 hover:text-green-700 transition-colors">
                                    <Check :size="16" />
                                </button>
                                <button type="button" @click="editing = null" class="p-1 text-neutral-400 hover:text-neutral-700 transition-colors">
                                    <X :size="16" />
                                </button>
                            </div>
                        </div>

                        <!-- View mode -->
                        <div v-else class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <!-- Image or icon -->
                                <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0">
                                    <img v-if="cat.image_url" :src="cat.image_url" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center"
                                        :style="{ backgroundColor: cat.color + '20', color: cat.color }">
                                        <font-awesome-icon v-if="cat.icon" :icon="['fas', cat.icon]" class="text-sm" />
                                        <div v-else class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: cat.color }" />
                                    </div>
                                </div>
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
