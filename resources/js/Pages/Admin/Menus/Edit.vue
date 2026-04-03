<script setup>
import MenuForm from '@/Components/Admin/MenuForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    menu_item: Object,
    pages: Array,
    parent_options: Array,
});

const form = useForm({
    label: props.menu_item.label,
    type: props.menu_item.type,
    target: props.menu_item.target ?? '',
    parent_id: props.menu_item.parent_id ?? '',
    sort_order: props.menu_item.sort_order,
    is_active: props.menu_item.is_active,
    open_in_new_tab: props.menu_item.open_in_new_tab,
});

function submit() {
    form.put(route('admin.menus.update', props.menu_item.id));
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Düzenle: ${menu_item.label}`" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.menus.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Menü Öğesini Düzenle</h1>
                <p class="mt-0.5 text-sm text-neutral-500">{{ menu_item.label }}</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <MenuForm :form="form" :pages="pages" :parent-options="parent_options" submit-label="Güncelle" />
        </form>
    </AdminLayout>
</template>
