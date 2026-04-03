<script setup>
import CategoryForm from '@/Components/Admin/CategoryForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    category: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.category.name,
    description: props.category.description ?? '',
    color: props.category.color ?? '#6366f1',
    icon: props.category.icon ?? '',
    image: null,
    remove_image: false,
});

function submit() {
    form.post(route('admin.categories.update', props.category.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Düzenle: ${category.name}`" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.categories.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Kategoriyi Düzenle</h1>
                <p class="mt-0.5 text-sm text-neutral-500">{{ category.name }}</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <CategoryForm :form="form" :category="category" submit-label="Güncelle" />
        </form>
    </AdminLayout>
</template>
