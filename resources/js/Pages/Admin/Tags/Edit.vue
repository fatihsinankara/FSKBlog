<script setup>
import TagForm from '@/Components/Admin/TagForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    tag: Object,
});

const form = useForm({
    name: props.tag.name,
});

function submit() {
    form.put(route('admin.tags.update', props.tag.id));
}
</script>

<template>
    <AdminLayout>
        <Head :title="`Düzenle: ${tag.name}`" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.tags.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Tag Düzenle</h1>
                <p class="mt-0.5 text-sm text-neutral-500">#{{ tag.name }}</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <TagForm :form="form" submit-label="Güncelle" />
        </form>
    </AdminLayout>
</template>
