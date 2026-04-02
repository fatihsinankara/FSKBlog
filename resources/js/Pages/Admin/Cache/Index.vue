<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { Database, RefreshCw, Trash2, ShieldCheck } from 'lucide-vue-next';

const props = defineProps({
    status: Object,
    recent_keys: Array,
});

const formatter = new Intl.DateTimeFormat('tr-TR', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

function formatDate(value) {
    return value ? formatter.format(new Date(value)) : 'Bilinmiyor';
}

function clearCache() {
    router.post(route('admin.cache.clear'), {}, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout>
        <Head title="Cache" />

        <div class="flex flex-col gap-3 mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Cache Durumu</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                    Uygulama cache katmaninin aktif durumunu gorebilir ve tek tikla temizleyebilirsin.
                </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                @click="clearCache"
            >
                <Trash2 :size="16" />
                Cache Temizle
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-8">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-indigo-50 p-2 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                    <Database :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Driver</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white">{{ status.driver }}</p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-sky-50 p-2 text-sky-600 dark:bg-sky-950 dark:text-sky-400">
                    <RefreshCw :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Toplam Key</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white">
                    {{ status.entry_count ?? 'N/A' }}
                </p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                    <ShieldCheck :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Blog Cache Key</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white">
                    {{ status.blog_key_count ?? 'N/A' }}
                </p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
                    <Database :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Content Versiyonu</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white">{{ status.blog_content_version }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <div>
                        <h2 class="font-semibold text-neutral-900 dark:text-white">Son Cache Anahtarlari</h2>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Database cache acikken en son gorulen anahtarlar burada listelenir.
                        </p>
                    </div>
                    <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                        {{ recent_keys.length }} kayit
                    </span>
                </div>

                <div v-if="status.supports_inspection && recent_keys.length" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <div
                        v-for="entry in recent_keys"
                        :key="entry.key"
                        class="flex flex-col gap-2 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <code class="text-xs text-neutral-700 dark:text-neutral-200 break-all">{{ entry.key }}</code>
                        <span class="shrink-0 text-xs text-neutral-500 dark:text-neutral-400">
                            {{ formatDate(entry.expires_at) }}
                        </span>
                    </div>
                </div>

                <div v-else class="px-6 py-8 text-sm text-neutral-500 dark:text-neutral-400">
                    <p v-if="!status.supports_inspection">
                        Bu cache driver anahtar listesi incelemeyi desteklemiyor. Yine de temizleme islemini kullanabilirsin.
                    </p>
                    <p v-else>
                        Su an listelenecek cache anahtari yok.
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                    <h2 class="font-semibold text-neutral-900 dark:text-white">Durum Ozeti</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-neutral-500 dark:text-neutral-400">Prefix</dt>
                            <dd class="text-right font-medium text-neutral-900 dark:text-white break-all">{{ status.prefix || 'Yok' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-neutral-500 dark:text-neutral-400">Lock Kaydi</dt>
                            <dd class="text-right font-medium text-neutral-900 dark:text-white">{{ status.lock_count ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-neutral-500 dark:text-neutral-400">Kategori Nav Cache</dt>
                            <dd class="text-right font-medium" :class="status.has_nav_categories ? 'text-emerald-600 dark:text-emerald-400' : 'text-neutral-900 dark:text-white'">
                                {{ status.has_nav_categories ? 'Hazir' : 'Bos' }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-neutral-500 dark:text-neutral-400">Son Kontrol</dt>
                            <dd class="text-right font-medium text-neutral-900 dark:text-white">{{ formatDate(status.inspected_at) }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                    <h2 class="font-semibold text-neutral-900 dark:text-white">Not</h2>
                    <p class="mt-3 text-sm leading-7 text-neutral-600 dark:text-neutral-300">
                        Bu panel uygulama cache anahtarlarini temizler. Icerik guncellemeleri sonraki istekte cache'i yeniden olusturur.
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
