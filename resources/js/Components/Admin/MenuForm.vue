<script setup>
import { computed, watch } from 'vue';
import { Save } from 'lucide-vue-next';

const props = defineProps({
    form: Object,
    pages: Array,
    categories: Array,
    parentOptions: Array,
    submitLabel: String,
});

function targetPlaceholder(type) {
    if (type === 'page') return 'Sayfa seç';
    if (type === 'category') return 'Kategori seç';

    return '/iletisim veya https://ornek.com';
}

const selectedPage = computed(() => props.pages.find((page) => page.slug === props.form.target) ?? null);
const selectedCategory = computed(() => props.categories.find((category) => category.slug === props.form.target) ?? null);
const labelLocked = computed(() => ['page', 'category'].includes(props.form.type));

watch(
    () => props.form.type,
    (type) => {
        if (type === 'page') {
            props.form.target = selectedPage.value?.slug ?? props.pages[0]?.slug ?? '';
        } else if (type === 'category') {
            props.form.target = selectedCategory.value?.slug ?? props.categories[0]?.slug ?? '';
        } else if (type !== 'custom') {
            props.form.target = '';
        }
    },
    { immediate: true }
);

watch(
    [() => props.form.type, selectedPage, selectedCategory],
    ([type, page, category]) => {
        if (type === 'page') {
            props.form.label = page?.title ?? '';
        } else if (type === 'category') {
            props.form.label = category?.name ?? '';
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_22rem]">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="mb-4 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Menü Öğesi</h2>

            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Görünen ad *</label>
                    <input
                        v-model="form.label"
                        type="text"
                        placeholder="Örn. Hakkımda"
                        class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        :class="[
                            { 'border-red-400': form.errors.label },
                            labelLocked ? 'cursor-not-allowed opacity-70' : ''
                        ]"
                        :disabled="labelLocked"
                    />
                    <p v-if="labelLocked" class="mt-1 text-xs text-neutral-500">
                        Görünen ad seçtiğin {{ form.type === 'page' ? 'sayfadan' : 'kategoriden' }} otomatik alınır.
                    </p>
                    <p v-if="form.errors.label" class="mt-1 text-xs text-red-500">{{ form.errors.label }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Tür</label>
                        <select
                            v-model="form.type"
                            class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="custom">Özel yol</option>
                            <option value="page">Sayfa</option>
                            <option value="category">Kategori</option>
                        </select>
                        <p v-if="form.errors.type" class="mt-1 text-xs text-red-500">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Üst öğe</label>
                        <select
                            v-model="form.parent_id"
                            class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="">Üst düzey</option>
                            <option v-for="parent in parentOptions" :key="parent.id" :value="parent.id">{{ parent.label }}</option>
                        </select>
                        <p v-if="form.errors.parent_id" class="mt-1 text-xs text-red-500">{{ form.errors.parent_id }}</p>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Hedef</label>
                    <select
                        v-if="form.type === 'page'"
                        v-model="form.target"
                        class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="">Sayfa seç...</option>
                        <option v-for="page in pages" :key="page.id" :value="page.slug">{{ page.title }}</option>
                    </select>
                    <select
                        v-else-if="form.type === 'category'"
                        v-model="form.target"
                        class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="">Kategori seç...</option>
                        <option v-for="category in categories" :key="category.id" :value="category.slug">{{ category.name }}</option>
                    </select>
                    <input
                        v-else
                        v-model="form.target"
                        type="text"
                        :placeholder="targetPlaceholder(form.type)"
                        class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    />
                    <p v-if="form.errors.target" class="mt-1 text-xs text-red-500">{{ form.errors.target }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-[10rem_1fr]">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Sıra</label>
                        <input
                            v-model.number="form.sort_order"
                            type="number"
                            min="0"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        />
                        <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-500">{{ form.errors.sort_order }}</p>
                    </div>

                    <div class="flex items-end gap-6 pb-2">
                        <label class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                            Aktif
                        </label>
                        <label class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                            <input v-model="form.open_in_new_tab" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                            Yeni sekmede aç
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700 disabled:opacity-50"
            >
                <Save :size="16" />
                {{ form.processing ? 'Kaydediliyor...' : submitLabel }}
            </button>
        </div>
    </div>
</template>
