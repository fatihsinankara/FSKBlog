<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Şifre Onayı" />

        <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-16">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Şifrenizi Onaylayın</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Devam etmek için şifrenizi girin</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-8">
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-5">
                        Bu, uygulamanın güvenli bir alanıdır. Devam etmeden önce şifrenizi onaylayın.
                    </p>

                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                                Şifre
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="current-password"
                                required
                                autofocus
                                class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.password }"
                                placeholder="••••••••"
                            />
                            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 px-4 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-xl transition-colors"
                        >
                            {{ form.processing ? 'Doğrulanıyor...' : 'Onayla' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
