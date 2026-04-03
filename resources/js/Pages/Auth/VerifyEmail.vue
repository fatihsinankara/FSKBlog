<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AppLayout>
        <Head title="E-posta Doğrulama" />

        <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-16">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">E-posta Doğrulama</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Devam etmek için e-postanızı doğrulayın</p>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-8">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-5">
                        Kayıt olduğunuz için teşekkürler! Devam etmeden önce size gönderdiğimiz bağlantıya tıklayarak e-posta adresinizi doğrulamanız gerekiyor. E-postayı almadıysanız tekrar gönderebiliriz.
                    </p>

                    <div
                        v-if="verificationLinkSent"
                        class="mb-5 text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-950/30 rounded-lg px-3 py-2"
                    >
                        Kayıt sırasında verdiğiniz e-posta adresine yeni bir doğrulama bağlantısı gönderildi.
                    </div>

                    <form @submit.prevent="submit" class="space-y-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 px-4 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white rounded-xl transition-colors"
                        >
                            {{ form.processing ? 'Gönderiliyor...' : 'Doğrulama E-postasını Tekrar Gönder' }}
                        </button>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full py-2.5 px-4 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-xl border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                        >
                            Çıkış Yap
                        </Link>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
