<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { formatDateTime } from '@/composables/useFormatDate';
import {
    Clock, Server, Terminal, Copy, Check,
    CircleCheck, CircleAlert, CalendarClock, RefreshCw,
} from 'lucide-vue-next';

const props = defineProps({
    cron_command: String,
    php_binary: String,
    base_path: String,
    server_info: Object,
    schedule: Array,
    last_run: String,
    cron_active: Boolean,
});

const copied = ref(false);

function copyCommand() {
    navigator.clipboard.writeText(props.cron_command).then(() => {
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    });
}

function ping() {
    router.post(route('admin.cron.ping'), {}, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <Head title="Cron" />

        <div class="flex flex-col gap-3 mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Cron Yonetimi</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                    Zamanlanmis gorevlerin durumunu ve sunucu cron yapilandirmasini buradan takip edebilirsin.
                </p>
            </div>
            <button
                @click="ping"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
            >
                <RefreshCw :size="16" />
                Manuel Ping
            </button>
        </div>

        <!-- Durum Kartlari -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-8">
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl p-2"
                    :class="cron_active
                        ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400'
                        : 'bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-400'"
                >
                    <CircleCheck v-if="cron_active" :size="18" />
                    <CircleAlert v-else :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Cron Durumu</p>
                <p class="mt-2 text-2xl font-semibold" :class="cron_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                    {{ cron_active ? 'Aktif' : 'Pasif' }}
                </p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-indigo-50 p-2 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                    <Clock :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Son Calisma</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white">
                    {{ last_run ? formatDateTime(last_run) : '—' }}
                </p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-sky-50 p-2 text-sky-600 dark:bg-sky-950 dark:text-sky-400">
                    <CalendarClock :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Zamanli Gorev</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white">{{ schedule.length }}</p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 inline-flex rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
                    <Server :size="18" />
                </div>
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">Sunucu</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-white truncate" :title="server_info.hostname">
                    {{ server_info.hostname }}
                </p>
            </div>
        </div>

        <!-- Uyari: Cron Pasif -->
        <div v-if="!cron_active" class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900 dark:bg-amber-950/30">
            <div class="flex gap-3">
                <CircleAlert :size="18" class="shrink-0 text-amber-600 dark:text-amber-400 mt-0.5" />
                <div>
                    <h3 class="font-semibold text-amber-800 dark:text-amber-300 text-sm">Cron Aktif Degil</h3>
                    <p class="mt-1 text-sm text-amber-700 dark:text-amber-400 leading-relaxed">
                        Son 5 dakika icinde scheduler calisma kaydi bulunamadi.
                        Asagidaki komutu sunucuya ekleyip cron'un calistiginden emin ol.
                    </p>
                </div>
            </div>
        </div>

        <!-- Cron Komutu -->
        <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900 mb-6">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                <div class="flex items-center gap-2">
                    <Terminal :size="16" class="text-neutral-500" />
                    <h2 class="font-semibold text-neutral-900 dark:text-white">Cron Komutu</h2>
                </div>
                <button
                    @click="copyCommand"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors"
                    :class="copied
                        ? 'border-emerald-300 dark:border-emerald-700 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30'
                        : 'border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800'"
                >
                    <Check v-if="copied" :size="12" />
                    <Copy v-else :size="12" />
                    {{ copied ? 'Kopyalandi' : 'Kopyala' }}
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-neutral-600 dark:text-neutral-300 mb-4">
                    Asagidaki komutu sunucunun crontab dosyasina eklemen gerekiyor. Bu komut her dakika Laravel scheduler'i calistirir.
                </p>
                <pre class="rounded-xl bg-neutral-950 p-4 text-sm text-emerald-400 font-mono overflow-x-auto leading-relaxed select-all">{{ cron_command }}</pre>
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1 text-xs text-neutral-500 dark:text-neutral-400">
                    <p>
                        <strong class="text-neutral-700 dark:text-neutral-300">Ekle:</strong>
                        <code class="ml-1 px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-xs">crontab -e</code>
                    </p>
                    <p>
                        <strong class="text-neutral-700 dark:text-neutral-300">Kontrol:</strong>
                        <code class="ml-1 px-1 py-0.5 rounded bg-neutral-100 dark:bg-neutral-800 text-xs">crontab -l</code>
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <!-- Zamanli Gorevler -->
            <div class="rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-800">
                    <h2 class="font-semibold text-neutral-900 dark:text-white">Zamanli Gorevler</h2>
                    <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                        {{ schedule.length }} gorev
                    </span>
                </div>

                <div v-if="schedule.length" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <div v-for="(task, i) in schedule" :key="i" class="px-6 py-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ task.command }}</p>
                                <span class="inline-flex items-center gap-1 mt-1.5 rounded-md bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 text-xs font-mono text-indigo-600 dark:text-indigo-400">
                                    <Clock :size="10" />
                                    {{ task.expression }}
                                </span>
                            </div>
                            <span class="shrink-0 text-xs text-neutral-500 dark:text-neutral-400 sm:mt-0.5">
                                {{ task.next_due }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-else class="px-6 py-8 text-sm text-neutral-500 dark:text-neutral-400">
                    Tanimlanmis zamanli gorev bulunamadi.
                </div>
            </div>

            <!-- Sunucu Bilgileri -->
            <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h2 class="font-semibold text-neutral-900 dark:text-white mb-4">Sunucu Bilgileri</h2>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">Hostname</dt>
                        <dd class="font-medium text-neutral-900 dark:text-white">{{ server_info.hostname }}</dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">PHP</dt>
                            <dd class="font-medium text-neutral-900 dark:text-white">{{ server_info.php_version }}</dd>
                        </div>
                        <div>
                            <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">Zaman Dilimi</dt>
                            <dd class="font-medium text-neutral-900 dark:text-white">{{ server_info.timezone }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">Isletim Sistemi</dt>
                        <dd class="font-medium text-neutral-900 dark:text-white">{{ server_info.os }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">PHP Binary</dt>
                        <dd class="font-mono text-xs text-neutral-700 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-800 rounded-lg px-3 py-2 break-all">{{ php_binary }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">Proje Dizini</dt>
                        <dd class="font-mono text-xs text-neutral-700 dark:text-neutral-300 bg-neutral-50 dark:bg-neutral-800 rounded-lg px-3 py-2 break-all">{{ base_path }}</dd>
                    </div>
                    <div>
                        <dt class="text-neutral-500 dark:text-neutral-400 text-xs uppercase tracking-wider mb-1">Sunucu Saati</dt>
                        <dd class="font-medium text-neutral-900 dark:text-white">{{ formatDateTime(server_info.server_time) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </AdminLayout>
</template>
