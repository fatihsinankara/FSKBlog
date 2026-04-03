<script setup>
import CollectionForm from '@/Components/Admin/CollectionForm.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    available_posts: Array,
});

const form = useForm({
    title: '',
    description: '',
    status: 'draft',
    items: props.available_posts.map((post) => ({
        id: post.id,
        title: post.title,
        slug: post.slug,
        category_name: post.category?.name ?? null,
        published_at: post.published_at,
        selected: false,
        part_number: '',
    })),
});

function selectedItemsPayload() {
    return form.items
        .filter((item) => item.selected)
        .map((item) => ({
            post_id: item.id,
            part_number: Number(item.part_number),
        }));
}

function submit() {
    form.transform(() => ({
        title: form.title,
        description: form.description,
        status: form.status,
        items: selectedItemsPayload(),
    })).post(route('admin.collections.store'));
}
</script>

<template>
    <AdminLayout>
        <Head title="Yeni Koleksiyon" />

        <div class="mb-8 flex items-center gap-4">
            <Link :href="route('admin.collections.index')" class="rounded-lg p-2 text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200">
                <ArrowLeft :size="20" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Yeni Koleksiyon</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Makaleleri belirli bir sıra ile seriye dönüştür.</p>
            </div>
        </div>

        <form @submit.prevent="submit">
            <CollectionForm :form="form" submit-label="Kaydet" />
        </form>
    </AdminLayout>
</template>
