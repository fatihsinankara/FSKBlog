<script setup>
import TagForm from '@/Components/Admin/TagForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
});

function submit() {
    form.post(route('admin.tags.store'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Yeni Tag" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.tags.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yeni Tag</h1>
        </div>

        <form @submit.prevent="submit">
            <TagForm :form="form" submit-label="Kaydet" />
        </form>
    </AdminLayout>
</template>
