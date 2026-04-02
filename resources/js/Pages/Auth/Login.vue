<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Giriş Yap" />

    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 flex items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">FSK Blog</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Admin paneline giriş yap</p>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-8">
                <div v-if="status" class="mb-4 text-sm text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                            E-posta
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            autofocus
                            class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.email }"
                            placeholder="admin@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

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
                            class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.password }"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">Beni hatırla</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 px-4 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-xl transition-colors"
                    >
                        {{ form.processing ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
