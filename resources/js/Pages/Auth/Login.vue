<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AuthTextField from '@/Components/Auth/AuthTextField.vue';
import TurnstileWidget from '@/Components/Shared/TurnstileWidget.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Lock, Mail, ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    status: String,
    canResetPassword: Boolean,
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
    cf_turnstile_response: '',
});

const turnstile = ref(null);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password', 'cf_turnstile_response');
            turnstile.value?.reset();
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Giriş Yap" />

        <div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-4 py-16">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 dark:ring-indigo-900/60">
                        <ShieldCheck :size="22" />
                    </div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Giriş Yap</h1>
                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Güvenli oturumla hesabına dön; korumalı formlar sessizce arka planda çalışır.
                    </p>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm shadow-neutral-950/5 dark:border-neutral-800 dark:bg-neutral-900 dark:shadow-black/20 sm:p-9">
                    <div v-if="status" class="mb-5 text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-950/30 rounded-lg px-3 py-2">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">
                        <AuthTextField
                            id="email"
                            v-model="form.email"
                            label="E-posta"
                            type="email"
                            autocomplete="email"
                            placeholder="ornek@mail.com"
                            required
                            autofocus
                            :error="form.errors.email"
                        >
                            <template #icon>
                                <Mail :size="17" />
                            </template>
                        </AuthTextField>

                        <AuthTextField
                            id="password"
                            v-model="form.password"
                            label="Şifre"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                            revealable
                            :error="form.errors.password"
                        >
                            <template #icon>
                                <Lock :size="17" />
                            </template>
                        </AuthTextField>

                        <div v-if="canResetPassword" class="-mt-3 flex justify-end">
                            <Link
                                :href="route('password.request')"
                                class="text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                            >
                                Şifremi unuttum
                            </Link>
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

                        <TurnstileWidget
                            ref="turnstile"
                            v-model="form.cf_turnstile_response"
                            :error="form.errors.cf_turnstile_response"
                            action="login"
                        />

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
                        </button>
                    </form>
                </div>

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
