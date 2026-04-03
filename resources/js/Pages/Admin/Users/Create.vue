<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
    email_verified: true,
});

function submit() {
    form.post(route('admin.users.store'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Yeni Kullanıcı" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.users.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yeni Kullanıcı</h1>
                <p class="mt-0.5 text-sm text-neutral-500">Yeni bir admin veya üye hesabı oluştur.</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <UserForm :form="form" submit-label="Kullanıcıyı Oluştur" :is-edit="false" />
        </form>
    </AdminLayout>
</template>
