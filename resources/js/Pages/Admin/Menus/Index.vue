<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmDialog from '@/Components/Shared/ConfirmDialog.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, ChevronRight, ExternalLink } from 'lucide-vue-next';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps({
    items: Array,
});

function displayTarget(item) {
    if (item.type === 'page') return `/p/${item.target}`;
    if (item.type === 'category') return `/categories/${item.target}`;

    return item.target;
}

const { confirmState, confirm, handleConfirm, handleCancel } = useConfirm();

function destroy(id) {
    confirm('Bu menü öğesini silmek istediğine emin misin?', () => {
        router.delete(route('admin.menus.destroy', id));
    });
}

function moveUp(list, index) {
    if (index === 0) return;
    reorder(list, index, index - 1);
}

function moveDown(list, index) {
    if (index === list.length - 1) return;
    reorder(list, index, index + 1);
}

function reorder(list, fromIndex, toIndex) {
    const ids = list.map((item) => item.id);
    const swapped = [...ids];
    [swapped[fromIndex], swapped[toIndex]] = [swapped[toIndex], swapped[fromIndex]];

    const payload = swapped.map((id, idx) => ({ id, sort_order: idx }));
    router.post(route('admin.menus.reorder'), { items: payload }, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <Head title="Menü Yönetimi" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Menü Yönetimi</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">NavBar'da görüntülenecek bağlantıları yönet</p>
        </div>

        <div class="mb-6 flex justify-end">
            <Link
                :href="route('admin.menus.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
            >
                <Plus :size="16" />
                Yeni Menü Öğesi
            </Link>
        </div>

        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Menü Öğeleri</h2>
                <span class="text-xs text-neutral-400">Sıra: ↑↓ butonları</span>
            </div>

            <div v-if="!items.length" class="px-6 py-10 text-center text-sm text-neutral-400">
                Henüz menü öğesi eklenmedi.
            </div>

            <div v-else class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <template v-for="(item, idx) in items" :key="item.id">
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex min-w-0 items-center gap-2">
                                <div class="flex flex-col gap-0.5 shrink-0">
                                    <button
                                        @click="moveUp(items, idx)"
                                        :disabled="idx === 0"
                                        class="leading-none text-neutral-300 transition-colors hover:text-neutral-500 disabled:opacity-20"
                                    >
                                        <span class="text-xs">▲</span>
                                    </button>
                                    <button
                                        @click="moveDown(items, idx)"
                                        :disabled="idx === items.length - 1"
                                        class="leading-none text-neutral-300 transition-colors hover:text-neutral-500 disabled:opacity-20"
                                    >
                                        <span class="text-xs">▼</span>
                                    </button>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-neutral-900 dark:text-white">{{ item.label }}</span>
                                        <span v-if="!item.is_active" class="rounded bg-neutral-100 px-1.5 py-0.5 text-xs text-neutral-400 dark:bg-neutral-800">pasif</span>
                                        <ExternalLink v-if="item.open_in_new_tab" :size="11" class="shrink-0 text-neutral-400" />
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-1">
                                        <span class="truncate font-mono text-xs text-neutral-400">{{ displayTarget(item) }}</span>
                                        <span class="rounded bg-indigo-50 px-1 text-xs text-indigo-400 dark:bg-indigo-950/40">{{ item.type }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <Link :href="route('admin.menus.edit', item.id)" class="p-1 text-neutral-400 transition-colors hover:text-indigo-600 dark:hover:text-indigo-400">
                                    <Pencil :size="14" />
                                </Link>
                                <button @click="destroy(item.id)" class="p-1 text-neutral-400 transition-colors hover:text-red-500">
                                    <Trash2 :size="14" />
                                </button>
                            </div>
                        </div>

                        <div v-if="item.children?.length" class="mt-2 ml-7 space-y-1">
                            <div v-for="(child, cidx) in item.children" :key="child.id" class="group flex items-center justify-between rounded-lg px-2 py-1 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                <div class="flex min-w-0 items-center gap-2">
                                    <div class="flex flex-col gap-0.5 shrink-0">
                                        <button
                                            @click="moveUp(item.children, cidx)"
                                            :disabled="cidx === 0"
                                            class="leading-none text-neutral-300 transition-colors hover:text-neutral-500 disabled:opacity-20"
                                        >
                                            <span class="text-xs">▲</span>
                                        </button>
                                        <button
                                            @click="moveDown(item.children, cidx)"
                                            :disabled="cidx === item.children.length - 1"
                                            class="leading-none text-neutral-300 transition-colors hover:text-neutral-500 disabled:opacity-20"
                                        >
                                            <span class="text-xs">▼</span>
                                        </button>
                                    </div>

                                    <ChevronRight :size="12" class="shrink-0 text-neutral-300" />

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ child.label }}</span>
                                            <span v-if="!child.is_active" class="rounded bg-neutral-100 px-1 text-xs text-neutral-400 dark:bg-neutral-800">pasif</span>
                                        </div>
                                        <span class="block truncate font-mono text-xs text-neutral-400">{{ displayTarget(child) }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0 opacity-0 transition-opacity group-hover:opacity-100">
                                    <Link :href="route('admin.menus.edit', child.id)" class="p-1 text-neutral-400 transition-colors hover:text-indigo-600 dark:hover:text-indigo-400">
                                        <Pencil :size="13" />
                                    </Link>
                                    <button @click="destroy(child.id)" class="p-1 text-neutral-400 transition-colors hover:text-red-500">
                                        <Trash2 :size="13" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <ConfirmDialog
            :open="confirmState.open"
            :message="confirmState.message"
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />
    </AdminLayout>
</template>
