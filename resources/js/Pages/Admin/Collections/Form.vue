<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';

const props = defineProps({
    mode: String,
    collection: Object,
    available_posts: Array,
});

function buildItems() {
    const assignedMap = new Map(
        (props.collection?.posts ?? []).map((post) => [
            post.id,
            post.pivot.part_number,
        ]),
    );

    return props.available_posts.map((post) => ({
        id: post.id,
        title: post.title,
        slug: post.slug,
        category_name: post.category?.name ?? null,
        published_at: post.published_at,
        selected: assignedMap.has(post.id),
        part_number: assignedMap.get(post.id) ?? '',
    })).sort((left, right) => {
        if (left.selected && right.selected) {
            return Number(left.part_number) - Number(right.part_number);
        }

        if (left.selected) return -1;
        if (right.selected) return 1;

        return left.title.localeCompare(right.title, 'tr');
    });
}

const form = useForm({
    title: props.collection?.title ?? '',
    description: props.collection?.description ?? '',
    status: props.collection?.status ?? 'draft',
    items: buildItems(),
});

function selectedItemsPayload() {
    return form.items
        .filter((item) => item.selected)
        .map((item) => ({
            post_id: item.id,
            part_number: Number(item.part_number),
        }));
}

function submit() {
    const payload = {
        title: form.title,
        description: form.description,
        status: form.status,
        items: selectedItemsPayload(),
    };

    if (props.mode === 'edit') {
        form.transform(() => payload).put(route('admin.collections.update', props.collection.id));
        return;
    }

    form.transform(() => payload).post(route('admin.collections.store'));
}

function nestedItemsError() {
    return Object.entries(form.errors).find(([key]) => key.startsWith('items.'))?.[1];
}

function toggleItem(item, checked) {
    item.selected = checked;

    if (!checked) {
        item.part_number = '';
    }
}

function formatDate(value) {
    if (!value) {
        return 'Tarih yok';
    }

    return new Date(value).toLocaleDateString('tr-TR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <AdminLayout>
        <Head :title="mode === 'edit' ? 'Koleksiyonu Duzenle' : 'Yeni Koleksiyon'" />

        <div class="flex items-center gap-4 mb-8">
            <Link :href="route('admin.collections.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                    {{ mode === 'edit' ? 'Koleksiyonu Duzenle' : 'Yeni Koleksiyon' }}
                </h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Makaleleri belirli bir sira ile seriye donustur.
                </p>
            </div>
        </div>

        <form class="grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.05fr]" @submit.prevent="submit">
            <div class="space-y-6">
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                    <h2 class="mb-4 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Koleksiyon Bilgileri</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-xs font-medium text-neutral-500">Baslik *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-medium text-neutral-500">Aciklama</label>
                            <textarea
                                v-model="form.description"
                                rows="5"
                                class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-medium text-neutral-500">Durum</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            >
                                <option value="draft">Taslak</option>
                                <option value="published">Yayina Al</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Secili Parcalar</h2>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                {{ form.items.filter((item) => item.selected).length }} makale secildi
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700 disabled:opacity-50"
                        >
                            <Save :size="16" />
                            {{ form.processing ? 'Kaydediliyor...' : (mode === 'edit' ? 'Guncelle' : 'Kaydet') }}
                        </button>
                    </div>

                    <p v-if="form.errors.items || nestedItemsError()" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                        {{ form.errors.items || nestedItemsError() }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h2 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Koleksiyondaki Yazilar</h2>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                        Bir yazi yalnizca tek koleksiyonda olabilir. Sectiginde part numarasi vermen gerekir.
                    </p>
                </div>

                <div class="max-h-[42rem] divide-y divide-neutral-100 overflow-y-auto dark:divide-neutral-800">
                    <label
                        v-for="item in form.items"
                        :key="item.id"
                        class="block px-6 py-4"
                    >
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex items-start gap-3">
                                    <input
                                        :checked="item.selected"
                                        type="checkbox"
                                        class="mt-0.5 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500"
                                        @change="toggleItem(item, $event.target.checked)"
                                    />
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-neutral-900 dark:text-white">
                                            {{ item.title }}
                                        </p>
                                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ item.category_name || 'Kategorisiz' }} • {{ formatDate(item.published_at) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 lg:pl-6">
                                <span class="text-xs uppercase tracking-[0.16em] text-neutral-400">Part</span>
                                <input
                                    v-model.number="item.part_number"
                                    :disabled="!item.selected"
                                    min="1"
                                    type="number"
                                    class="w-24 rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                />
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
