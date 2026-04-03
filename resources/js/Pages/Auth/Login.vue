<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
    canResetPassword: Boolean,
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
    <AppLayout>
        <Head title="Giriş Yap" />

        <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-16">
            <div class="w-full max-w-sm">
                <!-- Başlık -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Giriş Yap</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Hesabınıza hoş geldiniz</p>
                </div>

                <!-- Kart -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-8">
                    <div v-if="status" class="mb-5 text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-950/30 rounded-lg px-3 py-2">
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
                                placeholder="ornek@mail.com"
                            />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    Şifre
                                </label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                                >
                                    Şifremi unuttum
                                </Link>
                            </div>
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

                        <div class="flex items-center">
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

                <!-- Kayıt ol linki -->
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400 mt-5">
                    Hesabınız yok mu?
                    <Link :href="route('register')" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                        Kayıt Ol
                    </Link>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
