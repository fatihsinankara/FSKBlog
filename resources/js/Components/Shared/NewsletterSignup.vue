<script setup>
import { useForm } from '@inertiajs/vue3';
import { Mail, Send } from 'lucide-vue-next';

const props = defineProps({
    categoryId: Number,
    compact: { type: Boolean, default: false },
    title: { type: String, default: 'Yeni yazıları kaçırma' },
    description: { type: String, default: 'Yeni içerikler yayınlandığında e-posta ile haber al.' },
});

const form = useForm({
    email: '',
    name: '',
    frequency: 'instant',
    categories: props.categoryId ? [props.categoryId] : [],
});

function submit() {
    form.post(route('newsletter.subscribe'), {
        preserveScroll: true,
        onSuccess: () => form.reset('email', 'name'),
    });
}
</script>

<template>
    <section
        class="rounded-[1.75rem] border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
        :class="compact ? '' : 'mt-14'"
    >
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-300">
                <Mail :size="18" />
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-lg font-semibold text-neutral-950 dark:text-white">{{ title }}</h3>
                <p class="mt-1 text-sm leading-6 text-neutral-600 dark:text-neutral-300">{{ description }}</p>
            </div>
        </div>

        <form class="mt-5 space-y-4" @submit.prevent="submit">
            <div class="grid gap-3 sm:grid-cols-2">
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="İsim (opsiyonel)"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                />
                <input
                    v-model="form.email"
                    type="email"
                    placeholder="eposta@ornek.com"
                    class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    :class="{ 'border-red-400': form.errors.email }"
                />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-300">
                    <span>Sıklık</span>
                    <select
                        v-model="form.frequency"
                        class="rounded-lg border border-neutral-200 bg-white px-2.5 py-1.5 text-sm text-neutral-900 outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="instant">Anında</option>
                        <option value="weekly">Haftalık</option>
                    </select>
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-60"
                >
                    <Send :size="14" />
                    {{ form.processing ? 'Kaydediliyor...' : 'Abone Ol' }}
                </button>
            </div>

            <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
        </form>
    </section>
</template>
