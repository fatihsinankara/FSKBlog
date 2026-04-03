<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Shield, ShieldOff, Search, MailCheck } from 'lucide-vue-next';

const props = defineProps({
    users: Object,
    filters: Object,
});

const filterForm = useForm({
    q: props.filters.q || '',
});

function submitSearch() {
    filterForm.get(route('admin.users.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}

function destroyUser(user) {
    if (confirm(`${user.name} kullanıcısını silmek istediğine emin misin?`)) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true,
        });
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AdminLayout>
        <Head title="Kullanıcılar" />

        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kullanıcı Yönetimi</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Kayıtlı kullanıcıları, rollerini ve doğrulama durumlarını yönet.</p>
            </div>

            <Link
                :href="route('admin.users.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
            >
                <Plus :size="16" />
                Yeni Kullanıcı
            </Link>
        </div>

        <div class="mb-6 rounded-2xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <form @submit.prevent="submitSearch" class="flex flex-col gap-3 md:flex-row">
                <div class="relative flex-1">
                    <Search :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400" />
                    <input
                        v-model="filterForm.q"
                        type="text"
                        placeholder="İsim veya e-posta ile ara"
                        class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-3 pl-11 pr-4 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    />
                </div>
                <button
                    type="submit"
                    class="rounded-xl bg-neutral-900 px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                >
                    Filtrele
                </button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div v-if="!users.data.length" class="px-6 py-10 text-center text-sm text-neutral-400">
                Filtreye uygun kullanıcı bulunamadı.
            </div>

            <div v-else class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <div v-for="user in users.data" :key="user.id" class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="truncate text-sm font-semibold text-neutral-900 dark:text-white">{{ user.name }}</span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs"
                                :class="user.is_admin
                                    ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-300'
                                    : 'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-300'"
                            >
                                <component :is="user.is_admin ? Shield : ShieldOff" :size="11" />
                                {{ user.is_admin ? 'Admin' : 'Üye' }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs"
                                :class="user.email_verified_at
                                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-300'
                                    : 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-300'"
                            >
                                <MailCheck :size="11" />
                                {{ user.email_verified_at ? 'Doğrulanmış' : 'Bekliyor' }}
                            </span>
                        </div>
                        <p class="mt-1 truncate text-sm text-neutral-500 dark:text-neutral-400">{{ user.email }}</p>
                        <p class="mt-1 text-xs text-neutral-400">Kayıt: {{ formatDate(user.created_at) }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <Link
                            :href="route('admin.users.edit', user.id)"
                            class="inline-flex items-center gap-2 rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-600 transition-colors hover:border-indigo-200 hover:text-indigo-600 dark:border-neutral-700 dark:text-neutral-300 dark:hover:border-indigo-900 dark:hover:text-indigo-300"
                        >
                            <Pencil :size="14" />
                            Düzenle
                        </Link>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-neutral-200 px-3 py-2 text-sm text-neutral-500 transition-colors hover:border-red-200 hover:text-red-500 dark:border-neutral-700 dark:text-neutral-300 dark:hover:border-red-900 dark:hover:text-red-300"
                            @click="destroyUser(user)"
                        >
                            <Trash2 :size="14" />
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <Pagination v-if="users.links" :links="users.links" />
    </AdminLayout>
</template>
