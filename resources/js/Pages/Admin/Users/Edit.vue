<script setup>
import UserForm from '@/Components/Admin/UserForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    is_admin: props.user.is_admin,
    email_verified: !!props.user.email_verified_at,
});

function submit() {
    form.put(route('admin.users.update', props.user.id));
}
</script>

<template>
    <AdminLayout>
        <Head :title="`${user.name} Düzenle`" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.users.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kullanıcıyı Düzenle</h1>
                <p class="mt-0.5 text-sm text-neutral-500">{{ user.email }}</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <UserForm :form="form" submit-label="Güncelle" :is-edit="true" />
        </form>
    </AdminLayout>
</template>
