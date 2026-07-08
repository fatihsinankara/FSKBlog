<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TurnstileWidget from '@/Components/Shared/TurnstileWidget.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    cf_turnstile_response: '',
});

const turnstile = ref(null);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation', 'cf_turnstile_response');
            turnstile.value?.reset();
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Kayıt Ol" />

        <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-16">
            <div class="w-full max-w-sm">
                <!-- Başlık -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kayıt Ol</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Yeni bir hesap oluşturun</p>
                </div>

                <!-- Kart -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-8">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                                Ad Soyad
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                required
                                autofocus
                                class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.name }"
                                placeholder="Adınız Soyadınız"
                            />
                            <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                                E-posta
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="username"
                                required
                                class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.email }"
                                placeholder="ornek@mail.com"
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
                                autocomplete="new-password"
                                required
                                class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.password }"
                                placeholder="••••••••"
                            />
                            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">
                                Şifre Tekrar
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="w-full px-3 py-2.5 text-sm rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.password_confirmation }"
                                placeholder="••••••••"
                            />
                            <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-500">{{ form.errors.password_confirmation }}</p>
                        </div>

                        <TurnstileWidget
                            ref="turnstile"
                            v-model="form.cf_turnstile_response"
                            :error="form.errors.cf_turnstile_response"
                            action="register"
                        />

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 px-4 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-xl transition-colors"
                        >
                            {{ form.processing ? 'Hesap oluşturuluyor...' : 'Kayıt Ol' }}
                        </button>
                    </form>
                </div>

                <!-- Giriş yap linki -->
                <p class="text-center text-sm text-neutral-500 dark:text-neutral-400 mt-5">
                    Zaten hesabınız var mı?
                    <Link :href="route('login')" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                        Giriş Yap
                    </Link>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
