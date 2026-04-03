<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Check, X, ChevronRight, GripVertical, ExternalLink } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    items: Array,  // top-level items with .children
    pages: Array,  // published pages for the page picker
});

// ── Add form ──────────────────────────────────────────────
const form = useForm({
    label:           '',
    type:            'custom',
    target:          '',
    parent_id:       '',
    sort_order:      0,
    is_active:       true,
    open_in_new_tab: false,
});

function submit() {
    form.post(route('admin.menu-items.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

// ── Edit form ─────────────────────────────────────────────
const editing = ref(null);
const editForm = useForm({
    label:           '',
    type:            'custom',
    target:          '',
    parent_id:       '',
    sort_order:      0,
    is_active:       true,
    open_in_new_tab: false,
});

function startEdit(item) {
    editing.value = item.id;
    editForm.label           = item.label;
    editForm.type            = item.type;
    editForm.target          = item.target ?? '';
    editForm.parent_id       = item.parent_id ?? '';
    editForm.sort_order      = item.sort_order;
    editForm.is_active       = item.is_active;
    editForm.open_in_new_tab = item.open_in_new_tab;
}

function saveEdit(item) {
    editForm.put(route('admin.menu-items.update', item.id), {
        preserveScroll: true,
        onSuccess: () => { editing.value = null; },
    });
}

function destroy(id) {
    if (confirm('Bu menü öğesini silmek istediğine emin misin?')) {
        router.delete(route('admin.menu-items.destroy', id), { preserveScroll: true });
    }
}

// ── Reorder ───────────────────────────────────────────────
function moveUp(list, index) {
    if (index === 0) return;
    reorder(list, index, index - 1);
}

function moveDown(list, index) {
    if (index === list.length - 1) return;
    reorder(list, index, index + 1);
}

function reorder(list, fromIndex, toIndex) {
    const ids = list.map(i => i.id);
    const swapped = [...ids];
    [swapped[fromIndex], swapped[toIndex]] = [swapped[toIndex], swapped[fromIndex]];

    const payload = swapped.map((id, idx) => ({ id, sort_order: idx }));
    router.post(route('admin.menus.reorder'), { items: payload }, { preserveScroll: true });
}

// ── Helpers ───────────────────────────────────────────────
function topLevelItems() {
    return props.items;
}

function allItems() {
    const result = [];
    props.items.forEach(item => {
        result.push(item);
        (item.children ?? []).forEach(child => result.push(child));
    });
    return result;
}

function parentOptions() {
    return props.items; // only top-level can be parents
}

function targetPlaceholder(type) {
    if (type === 'page') return 'Sayfa seç';
    if (type === 'external') return 'https://ornek.com';
    return '/kategoriler';
}
</script>

<template>
    <AdminLayout>
        <Head title="Menü Yönetimi" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Menü Yönetimi</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">NavBar'da görüntülenecek bağlantıları yönet</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- ── Add form ── -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-6">
                <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-4">Yeni Öğe Ekle</h2>
                <form @submit.prevent="submit" class="space-y-3">

                    <div>
                        <input v-model="form.label" type="text" placeholder="Görünen ad *"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            :class="{ 'border-red-400': form.errors.label }" />
                        <p v-if="form.errors.label" class="text-xs text-red-500 mt-1">{{ form.errors.label }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-neutral-500 mb-1">Tür</label>
                            <select v-model="form.type"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="custom">Özel yol</option>
                                <option value="page">Sayfa</option>
                                <option value="external">Dış bağlantı</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-neutral-500 mb-1">Üst öğe (opsiyonel)</label>
                            <select v-model="form.parent_id"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Üst düzey —</option>
                                <option v-for="p in parentOptions()" :key="p.id" :value="p.id">{{ p.label }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Page picker -->
                    <div v-if="form.type === 'page'">
                        <label class="block text-xs text-neutral-500 mb-1">Sayfa</label>
                        <select v-model="form.target"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Sayfa seç...</option>
                            <option v-for="pg in pages" :key="pg.id" :value="pg.slug">{{ pg.title }}</option>
                        </select>
                    </div>

                    <!-- URL input -->
                    <div v-else>
                        <label class="block text-xs text-neutral-500 mb-1">Hedef</label>
                        <input v-model="form.target" type="text" :placeholder="targetPlaceholder(form.type)"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400 cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="rounded" />
                            Aktif
                        </label>
                        <label class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400 cursor-pointer">
                            <input v-model="form.open_in_new_tab" type="checkbox" class="rounded" />
                            Yeni sekmede aç
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs text-neutral-500 mb-1">Sıra</label>
                        <input v-model.number="form.sort_order" type="number" min="0"
                            class="w-24 px-3 py-2 text-sm rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition-colors">
                        <Plus :size="16" />
                        Ekle
                    </button>
                </form>
            </div>

            <!-- ── Item list ── -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-800 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Menü Öğeleri</h2>
                    <span class="text-xs text-neutral-400">Sıra: ↑↓ butonları</span>
                </div>

                <div v-if="!items.length" class="px-6 py-10 text-center text-sm text-neutral-400">
                    Henüz menü öğesi eklenmedi.
                </div>

                <div v-else class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <template v-for="(item, idx) in items" :key="item.id">

                        <!-- ── Edit mode (top-level) ── -->
                        <div v-if="editing === item.id" class="px-5 py-4 space-y-2 bg-indigo-50/40 dark:bg-indigo-950/20">
                            <input v-model="editForm.label" type="text" placeholder="Görünen ad"
                                class="w-full px-2 py-1 text-sm rounded border border-indigo-400 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                            <div class="grid grid-cols-2 gap-2">
                                <select v-model="editForm.type"
                                    class="px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none">
                                    <option value="custom">Özel yol</option>
                                    <option value="page">Sayfa</option>
                                    <option value="external">Dış bağlantı</option>
                                </select>
                                <input v-model.number="editForm.sort_order" type="number" min="0" placeholder="Sıra"
                                    class="px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                            </div>
                            <select v-if="editForm.type === 'page'" v-model="editForm.target"
                                class="w-full px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none">
                                <option value="">Sayfa seç...</option>
                                <option v-for="pg in pages" :key="pg.id" :value="pg.slug">{{ pg.title }}</option>
                            </select>
                            <input v-else v-model="editForm.target" type="text" :placeholder="targetPlaceholder(editForm.type)"
                                class="w-full px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-1.5 text-xs text-neutral-500 cursor-pointer">
                                    <input v-model="editForm.is_active" type="checkbox" class="rounded" /> Aktif
                                </label>
                                <label class="flex items-center gap-1.5 text-xs text-neutral-500 cursor-pointer">
                                    <input v-model="editForm.open_in_new_tab" type="checkbox" class="rounded" /> Yeni sekme
                                </label>
                            </div>
                            <div class="flex items-center justify-end gap-1">
                                <button @click="saveEdit(item)" class="p-1 text-green-600 hover:text-green-700 transition-colors">
                                    <Check :size="16" />
                                </button>
                                <button @click="editing = null" class="p-1 text-neutral-400 hover:text-neutral-600 transition-colors">
                                    <X :size="16" />
                                </button>
                            </div>
                        </div>

                        <!-- ── View mode (top-level) ── -->
                        <div v-else class="px-5 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="flex flex-col gap-0.5 shrink-0">
                                        <button @click="moveUp(items, idx)" :disabled="idx === 0"
                                            class="text-neutral-300 hover:text-neutral-500 disabled:opacity-20 transition-colors leading-none">
                                            <span class="text-xs">▲</span>
                                        </button>
                                        <button @click="moveDown(items, idx)" :disabled="idx === items.length - 1"
                                            class="text-neutral-300 hover:text-neutral-500 disabled:opacity-20 transition-colors leading-none">
                                            <span class="text-xs">▼</span>
                                        </button>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-neutral-900 dark:text-white">{{ item.label }}</span>
                                            <span v-if="!item.is_active" class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-800 px-1.5 py-0.5 rounded">pasif</span>
                                            <ExternalLink v-if="item.open_in_new_tab" :size="11" class="text-neutral-400 shrink-0" />
                                        </div>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <span class="text-xs text-neutral-400 font-mono truncate">{{ item.type === 'page' ? '/p/' + item.target : item.target }}</span>
                                            <span class="text-xs text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 px-1 rounded">{{ item.type }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click="startEdit(item)" class="p-1 text-neutral-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        <Pencil :size="14" />
                                    </button>
                                    <button @click="destroy(item.id)" class="p-1 text-neutral-400 hover:text-red-500 transition-colors">
                                        <Trash2 :size="14" />
                                    </button>
                                </div>
                            </div>

                            <!-- Children -->
                            <div v-if="item.children?.length" class="mt-2 ml-7 space-y-1">
                                <div v-for="(child, cidx) in item.children" :key="child.id">

                                    <!-- Child edit mode -->
                                    <div v-if="editing === child.id" class="p-2 rounded-lg bg-indigo-50/40 dark:bg-indigo-950/20 space-y-2">
                                        <input v-model="editForm.label" type="text" placeholder="Görünen ad"
                                            class="w-full px-2 py-1 text-sm rounded border border-indigo-400 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                                        <div class="grid grid-cols-2 gap-2">
                                            <select v-model="editForm.type"
                                                class="px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none">
                                                <option value="custom">Özel yol</option>
                                                <option value="page">Sayfa</option>
                                                <option value="external">Dış bağlantı</option>
                                            </select>
                                            <input v-model.number="editForm.sort_order" type="number" min="0"
                                                class="px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                                        </div>
                                        <select v-if="editForm.type === 'page'" v-model="editForm.target"
                                            class="w-full px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none">
                                            <option value="">Sayfa seç...</option>
                                            <option v-for="pg in pages" :key="pg.id" :value="pg.slug">{{ pg.title }}</option>
                                        </select>
                                        <input v-else v-model="editForm.target" type="text" :placeholder="targetPlaceholder(editForm.type)"
                                            class="w-full px-2 py-1 text-sm rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white focus:outline-none" />
                                        <div class="flex items-center gap-3">
                                            <label class="flex items-center gap-1.5 text-xs text-neutral-500 cursor-pointer">
                                                <input v-model="editForm.is_active" type="checkbox" class="rounded" /> Aktif
                                            </label>
                                            <label class="flex items-center gap-1.5 text-xs text-neutral-500 cursor-pointer">
                                                <input v-model="editForm.open_in_new_tab" type="checkbox" class="rounded" /> Yeni sekme
                                            </label>
                                        </div>
                                        <div class="flex items-center justify-end gap-1">
                                            <button @click="saveEdit(child)" class="p-1 text-green-600 hover:text-green-700 transition-colors">
                                                <Check :size="14" />
                                            </button>
                                            <button @click="editing = null" class="p-1 text-neutral-400 hover:text-neutral-600 transition-colors">
                                                <X :size="14" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Child view mode -->
                                    <div v-else class="flex items-center justify-between px-2 py-1 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 group">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="flex flex-col gap-0.5 shrink-0">
                                                <button @click="moveUp(item.children, cidx)" :disabled="cidx === 0"
                                                    class="text-neutral-300 hover:text-neutral-500 disabled:opacity-20 transition-colors leading-none">
                                                    <span class="text-xs">▲</span>
                                                </button>
                                                <button @click="moveDown(item.children, cidx)" :disabled="cidx === item.children.length - 1"
                                                    class="text-neutral-300 hover:text-neutral-500 disabled:opacity-20 transition-colors leading-none">
                                                    <span class="text-xs">▼</span>
                                                </button>
                                            </div>
                                            <ChevronRight :size="12" class="text-neutral-300 shrink-0" />
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ child.label }}</span>
                                                    <span v-if="!child.is_active" class="text-xs text-neutral-400 bg-neutral-100 dark:bg-neutral-800 px-1 rounded">pasif</span>
                                                </div>
                                                <span class="text-xs text-neutral-400 font-mono truncate block">{{ child.type === 'page' ? '/p/' + child.target : child.target }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="startEdit(child)" class="p-1 text-neutral-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                <Pencil :size="13" />
                                            </button>
                                            <button @click="destroy(child.id)" class="p-1 text-neutral-400 hover:text-red-500 transition-colors">
                                                <Trash2 :size="13" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
