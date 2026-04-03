<script setup>
import { Save } from 'lucide-vue-next';

defineProps({
    form: Object,
    submitLabel: String,
    isEdit: Boolean,
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_22rem]">
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="mb-4 text-sm font-semibold text-neutral-700 dark:text-neutral-300">Kullanıcı Bilgileri</h2>

            <div class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Ad Soyad *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            :class="{ 'border-red-400': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">E-posta *</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            :class="{ 'border-red-400': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            {{ isEdit ? 'Yeni şifre' : 'Şifre *' }}
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            :class="{ 'border-red-400': form.errors.password }"
                        />
                        <p class="mt-1 text-xs text-neutral-500">
                            {{ isEdit ? 'Boş bırakırsan mevcut şifre korunur.' : 'Giriş için kullanılacak güvenli bir şifre belirle.' }}
                        </p>
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Şifre tekrarı</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        />
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6 rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 dark:border-neutral-800 dark:bg-neutral-950/50">
                    <label class="flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300">
                        <input v-model="form.is_admin" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                        Admin yetkisi
                    </label>
                    <label class="flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300">
                        <input v-model="form.email_verified" type="checkbox" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                        E-posta doğrulanmış
                    </label>
                </div>

                <p v-if="form.errors.is_admin" class="text-xs text-red-500">{{ form.errors.is_admin }}</p>
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
