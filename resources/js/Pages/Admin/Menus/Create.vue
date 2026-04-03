<script setup>
import MenuForm from '@/Components/Admin/MenuForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    pages: Array,
    parent_options: Array,
});

const form = useForm({
    label: '',
    type: 'custom',
    target: '',
    parent_id: '',
    sort_order: 0,
    is_active: true,
    open_in_new_tab: false,
});

function submit() {
    form.post(route('admin.menus.store'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Yeni Menü Öğesi" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.menus.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yeni Menü Öğesi</h1>
        </div>

        <form @submit.prevent="submit">
            <MenuForm :form="form" :pages="pages" :parent-options="parent_options" submit-label="Kaydet" />
        </form>
    </AdminLayout>
</template>
