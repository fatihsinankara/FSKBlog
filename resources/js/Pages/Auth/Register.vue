<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AuthTextField from '@/Components/Auth/AuthTextField.vue';
import TurnstileWidget from '@/Components/Shared/TurnstileWidget.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Lock, Mail, ShieldCheck, User } from 'lucide-vue-next';
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

        <div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-4 py-16">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 dark:ring-indigo-900/60">
                        <ShieldCheck :size="22" />
                    </div>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kayıt Ol</h1>
                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Hesabını oluştur, e-posta doğrulamasını tamamla ve içerikleri kaydetmeye başla.
                    </p>
                </div>

                <div class="rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm shadow-neutral-950/5 dark:border-neutral-800 dark:bg-neutral-900 dark:shadow-black/20 sm:p-9">
                    <form @submit.prevent="submit" class="space-y-5">
                        <AuthTextField
                            id="name"
                            v-model="form.name"
                            label="Ad Soyad"
                            autocomplete="name"
                            placeholder="Adınız Soyadınız"
                            required
                            autofocus
                            :error="form.errors.name"
                        >
                            <template #icon>
                                <User :size="17" />
                            </template>
                        </AuthTextField>

                        <AuthTextField
                            id="email"
                            v-model="form.email"
                            label="E-posta"
                            type="email"
                            autocomplete="username"
                            placeholder="ornek@mail.com"
                            required
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
                            autocomplete="new-password"
                            placeholder="••••••••"
                            required
                            revealable
                            :error="form.errors.password"
                        >
                            <template #icon>
                                <Lock :size="17" />
                            </template>
                        </AuthTextField>

                        <AuthTextField
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            label="Şifre Tekrar"
                            type="password"
                            autocomplete="new-password"
                            placeholder="••••••••"
                            required
                            revealable
                            :error="form.errors.password_confirmation"
                        >
                            <template #icon>
                                <Lock :size="17" />
                            </template>
                        </AuthTextField>

                        <TurnstileWidget
                            ref="turnstile"
                            v-model="form.cf_turnstile_response"
                            :error="form.errors.cf_turnstile_response"
                            action="register"
                        />

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ form.processing ? 'Hesap oluşturuluyor...' : 'Kayıt Ol' }}
                        </button>
                    </form>
                </div>

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
