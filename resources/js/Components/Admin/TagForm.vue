<script setup>
import { Save } from 'lucide-vue-next';

defineProps({
    form: Object,
    submitLabel: String,
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_18rem]">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="mb-4 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Tag Bilgileri</h2>

            <div>
                <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Ad *</label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Tag adı"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    :class="{ 'border-red-400': form.errors.name }"
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
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
